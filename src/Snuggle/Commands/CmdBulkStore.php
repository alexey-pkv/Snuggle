<?php
namespace Snuggle\Commands;


use Snuggle\Commands\Store\TCmdBulkResolve;
use Structura\Arrays;

use Snuggle\Base\IConnection;
use Snuggle\Base\Commands\ICmdBulkStore;
use Snuggle\Base\Commands\Store\IBulkStoreResult;
use Snuggle\Base\Conflict\Resolvers\IBulkStoreResolution;
use Snuggle\Base\Connection\Response\IRawResponse;

use Snuggle\Commands\Store\BulkStoreSet;
use Snuggle\Commands\Store\ResponseParser;

use Snuggle\Connection\Method;
use Snuggle\Exceptions\HttpException;
use Snuggle\Exceptions\FatalSnuggleException;
use Snuggle\Exceptions\Http\ConflictException;
use Snuggle\Connection\Request\RawRequest;


class CmdBulkStore implements ICmdBulkStore
{
	use TCmdBulkResolve;
	
	
	private $db;
	private $retires = null;
	
	/** @var IConnection */
	private $connection;
	
	/** @var IBulkStoreResolution */
	private $resolver;
	
	/** @var BulkStoreSet */
	private $data;
	
	
	private function executeRequest(ConflictException &$e = null): IRawResponse
	{
		if (!$this->db)
			throw new FatalSnuggleException('Database name must be set!');
		
		$docs = array_values($this->data->Pending);
		$body = ['docs' => $docs];
		
		$request = RawRequest::create("/{$this->db}/_bulk_docs", Method::POST);
		$request->setBody($body);
		
		return $this->connection->request($request);
	}
	
	private function getRetries(?int $maxRetries): int
	{
		if (!is_null($maxRetries))
			return max($maxRetries, 0);
		
		if (!is_null($this->retires))
			return max($maxRetries, 0);
		
		return PHP_INT_MAX;
	}
	
	
	public function __construct(IConnection $connection)
	{
		$this->connection	= $connection;
		$this->data			= new BulkStoreSet();
		
		$this->ignoreConflict();
	}
	
	
	/**
	 * @param string $db
	 * @return ICmdBulkStore|static
	 */
	public function into(string $db): ICmdBulkStore
	{
		$this->db = $db;
		return $this;
	}
	
	public function setCostumeResolver(IBulkStoreResolution $resolver): ICmdBulkStore
	{
		$resolver->setConnection($this->connection);
		$resolver->setStore($this->data);
		$this->resolver = $resolver;
		return $this;
	}
	
	/**
	 * @param array|string $id Document ID or the document itself.
	 * @param array|string|null $rev Document revision, or the document itself.
	 * @param array|null $data Document to store. If set, $id must be string.
	 * @return ICmdBulkStore|static
	 */
	public function data($id, $rev = null, ?array $data = null): ICmdBulkStore
	{
		if (is_array($id))
			$data = $id;
		else if (is_array($rev))
			$data = $rev;
		else if (!is_array($data))
			throw new FatalSnuggleException('No document provided');
		
		if (is_scalar($id))
			$data['_id'] = (string)$id;
		
		if (is_scalar($rev))
			$data['_rev'] = (string)$rev;
		
		$this->data->addDocument($data);
		
		return $this;
	}
	
	public function dataSet(array $data, bool $isAssoc = false): ICmdBulkStore
	{
		if (is_null($isAssoc))
			$isAssoc = Arrays::isAssoc($data);
		
		if ($isAssoc)
		{
			foreach ($data as $key => $value)
			{
				$value['_id'] = (string)$key;
			}
		}
		
		$this->data->addDocuments($data);
		return $this;
	}
	
	public function setMaxRetries(?int $maxRetries = null): ICmdBulkStore
	{
		$this->retires = $maxRetries;
		return $this;
	}
	
	public function execute(?int $maxRetries = null): IBulkStoreResult
	{
		$retires = $this->getRetries($maxRetries);
		$doRetry = true;
		
		while ($retires-- > 0 && $doRetry)
		{
			$this->data->TotalRequests++;
			
			$response = $this->executeRequest();
			
			try
			{
				ResponseParser::parse($this->data, $response);
			}
			catch (ConflictException $ce)
			{
				$doRetry = $this->resolver->resolve($ce, $response);
			}
		}
		
		return $this->data;
	}
	
	public function executeSafe(\Exception &$e = null, ?int $maxRetries = null): ?IBulkStoreResult
	{
		try
		{
			$this->execute($maxRetries);
		}
		catch (HttpException $he)
		{
			$e = $he;
		}
		catch (\Throwable $t)
		{
			$e = $t;
			return null;
		}
		
		return $this->data;
	}
}
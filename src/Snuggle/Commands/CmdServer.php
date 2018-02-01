<?php
namespace Snuggle\Commands;


use Snuggle\Core\Server\Index;
use Snuggle\Base\Commands\ICmdServer;
use Snuggle\Commands\Abstraction\AbstractToolSetCommand;
use Snuggle\Exceptions\SnuggleException;


class CmdServer extends AbstractToolSetCommand implements ICmdServer
{
	public function info(): Index
	{
		$result = $this->executeRequest('/');
		
		if ($result->isFailed())
			throw new SnuggleException('Query Failed');
		
		$result = $result->getBody()->getJson(true);
		$info = new Index();
		
		$info->UUID				= $result['uuid'] ?? '';
		$info->Version			= $result['version'] ?? '';
		$info->Vendor->Name		= $result['vendor']['name'] ?? '';
		$info->Vendor->Version	= $result['vendor']['version'] ?? '';
		
		return $info;
	}
}
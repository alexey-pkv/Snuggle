<?php
namespace Snuggle\Factories\Commands;


use Snuggle\Base\IConnector;
use Snuggle\Base\IConnection;

use Snuggle\Base\Commands\ICmdDB;
use Snuggle\Base\Commands\ICmdGet;
use Snuggle\Base\Commands\ICmdStore;
use Snuggle\Base\Commands\ICmdDesign;
use Snuggle\Base\Commands\ICmdDelete;
use Snuggle\Base\Commands\ICmdInsert;
use Snuggle\Base\Commands\ICmdDirect;
use Snuggle\Base\Commands\ICmdServer;

use Snuggle\Base\Commands\ICmdBulkGet;
use Snuggle\Base\Commands\ICmdBulkStore;
use Snuggle\Base\Commands\ICmdBulkInsert;

use Snuggle\Base\Factories\ICommandFactory;

use Snuggle\Commands\CmdDB;
use Snuggle\Commands\CmdGet;
use Snuggle\Commands\CmdStore;
use Snuggle\Commands\CmdDesign;
use Snuggle\Commands\CmdDelete;
use Snuggle\Commands\CmdDirect;
use Snuggle\Commands\CmdInsert;
use Snuggle\Commands\CmdServer;

use Snuggle\Commands\CmdBulkGet;
use Snuggle\Commands\CmdBulkStore;
use Snuggle\Commands\CmdBulkInsert;


class SimpleFactory implements ICommandFactory
{
	public function db(IConnection $connection): ICmdDB
	{
		return new CmdDB($connection);
	}
	
	public function direct(IConnection $connection): ICmdDirect
	{
		return new CmdDirect($connection);
	}
	
	public function server(IConnection $connection): ICmdServer
	{
		return new CmdServer($connection);
	}
	
	public function get(IConnection $connection): ICmdGet
	{
		return new CmdGet($connection);
	}
	
	public function delete(IConnection $connection): ICmdDelete
	{
		return new CmdDelete($connection);
	}
	
	public function insert(IConnection $connection): ICmdInsert
	{
		return new CmdInsert($connection);
	}
	
	public function store(IConnection $connection): ICmdStore
	{
		return new CmdStore($connection);
	}
	
	public function getAll(IConnection $connection): ICmdBulkGet
	{
		return new CmdBulkGet($connection);
	}
	
	public function storeAll(IConnector $connector, IConnection $connection): ICmdBulkStore
	{
		return new CmdBulkStore($connector, $connection);
	}
	
	public function insertAll(IConnection $connection): ICmdBulkInsert
	{
		return new CmdBulkInsert($connection);
	}
	
	public function design(IConnection $connection): ICmdDesign
	{
		return new CmdDesign($this->store($connection));
	}
}
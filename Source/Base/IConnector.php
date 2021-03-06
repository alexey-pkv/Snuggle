<?php
namespace Snuggle\Base;


use Snuggle\Base\Commands\ICmdDB;
use Snuggle\Base\Commands\ICmdGet;
use Snuggle\Base\Commands\ICmdStore;
use Snuggle\Base\Commands\ICmdDesign;
use Snuggle\Base\Commands\ICmdDelete;
use Snuggle\Base\Commands\ICmdInsert;
use Snuggle\Base\Commands\ICmdServer;
use Snuggle\Base\Commands\ICmdDirect;

use Snuggle\Base\Commands\ICmdBulkGet;
use Snuggle\Base\Commands\ICmdBulkStore;
use Snuggle\Base\Commands\ICmdBulkInsert;


interface IConnector
{
	public function db(): ICmdDB; 
	public function get(): ICmdGet;
	public function store(): ICmdStore;
	public function insert(): ICmdInsert;
	public function delete(): ICmdDelete;
	public function server(): ICmdServer;
	public function direct(): ICmdDirect;
	
	public function design(): ICmdDesign;
	
	public function getAll(): ICmdBulkGet;
	public function storeAll(): ICmdBulkStore;
	public function insertAll(): ICmdBulkInsert;
	
	/**
	 * @deprecated
	 * @return ICmdBulkInsert
	 */
	public function bulkInsert(): ICmdBulkInsert;
}
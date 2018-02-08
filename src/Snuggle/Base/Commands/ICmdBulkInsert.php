<?php
namespace Snuggle\Base\Commands;


use Snuggle\Core\Doc;


interface ICmdBulkInsert extends IExecute, IQuery
{
	/**
	 * @param string $db
	 * @return ICmdBulkInsert|static
	 */
	public function into(string $db): ICmdBulkInsert;
	
	/**
	 * @param array|\stdClass
	 * @return ICmdBulkInsert|static
	 */
	public function document($document): ICmdBulkInsert;
	
	/**
	 * @param array[]|\stdClass[]
	 * @return ICmdBulkInsert|static
	 */
	public function documents(array $documents): ICmdBulkInsert;
	
	/**
	 * @return string[]
	 */
	public function queryIDs(): array;
	
	/**
	 * @return bool[]
	 */
	public function queryIsSuccessful(): array;
	
	/**
	 * @return Doc[]
	 */
	public function queryDocs(): array;
}
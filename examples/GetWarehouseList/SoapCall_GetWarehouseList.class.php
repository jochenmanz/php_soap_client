<?php

require_once ROOT.'lib/soap/call/PlentySoapCall.abstract.php';

class SoapCall_GetWarehouseList extends PlentySoapCall 
{
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}
	
	public function execute()
	{
		try
		{
			$this->getLogger()->debug(__FUNCTION__.' start');
	
			/*
			 * do soap call
			*/
			$response	=	$this->getPlentySoap()->GetWarehouseList(new PlentySoapRequest_GetWarehouseList);
	
			/*
			 * check soap response
			*/
			if( $response->Success == true )
			{
				$this->getLogger()->debug(__FUNCTION__.' Request Success - : GetWarehouseList');
				
				/*
				 * parse and save the data
				 */
				$this->parseResponse($response);
			}
			else
			{
				$this->getLogger()->debug(__FUNCTION__.' Request Error');
			}
		}
		catch(Exception $e)
		{
			$this->onExceptionAction($e);
		}
	}
	
	/**
	 * Parse the response
	 *
	 * @param PlentySoapResponse_GetWarehouseList $response
	 */
	private function parseResponse($response)
	{
		if(is_array($response->WarehouseList->item))
		{
			/*
			 * If more than one warehouse
			*/
			foreach ($response->WarehouseList->item as $warehouse)
			{
				$this->saveInDatabase($warehouse);
			}
		}
		/*
		 * only one warehouse
		*/
		elseif (is_object($response->WarehouseList->item))
		{
			$this->saveInDatabase($response->WarehouseList->item);
		}
	}
	
	/**
	 * Save the data in the database
	 *
	 * @param PlentySoapObject_GetWarehouseList $warehouse
	 */
	private function saveInDatabase($warehouse)
	{
		$query = 'REPLACE INTO `plenty_warehouse` '.
								DBUtils::buildInsert(	array(	'warehouse_id'		=>	$warehouse->WarehouseID,
																'warehouse_type'	=>	$warehouse->Type,
																'warehouse_name'	=>	$warehouse->Name
														)
								);
	
		$this->getLogger()->debug(__FUNCTION__.' '.$query);

		DBQuery::getInstance()->replace($query);
	}
}

?>
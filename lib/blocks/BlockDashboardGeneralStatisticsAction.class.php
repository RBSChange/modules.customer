<?php
/**
 * customer_BlockDashboardGeneralStatisticsAction
 * @package modules.customer.lib.blocks
 */
class customer_BlockDashboardGeneralStatisticsAction extends dashboard_BlockDashboardAction
{	
	/**
	 * @param f_mvc_Request $request
	 * @param boolean $forEdition
	 */
	protected function setRequestContent($request, $forEdition)
	{
		if ($request->hasParameter('shopId'))
		{
			$website = DocumentHelper::getDocumentInstance($request->getParameter('websiteId'));
		}
		else
		{
			$website = $this->getConfiguration()->getWebsite();
		}
		if ($forEdition || !$website)
		{
			return;
		}
		
		$websiteId = $website->getId();
		$configuration = $this->getConfiguration();
		if (!$configuration->getUseCharts())
		{
			$widget = array();
			$os = customer_CustomerService::getInstance();
			$fromDate = $toDate = null;
			for ($m = 0 ; $m < 6 ; $m++)
			{
				$this->initPreviousMonthDates($fromDate, $toDate, $m);
				$widget['lines'][] = $os->getStatisticsByWebsite($website, $fromDate, $toDate);
			}
			$columns = array();
			foreach (explode(',', $configuration->getColumns()) as $columnName)
			{
				$columns[$columnName] = true;
			}			
			$request->setAttribute('columnsArray', $columns);
			$request->setAttribute('widget', $widget);
		}
		else
		{
			$charts = array();			
			foreach (explode(',', $configuration->getColumns()) as $columnName)
			{
				$producer = new customer_WebsiteBasicStatisticsProducer();
				$chart = new f_chart_BarChart($producer->getDataTable(array('websiteId' => $websiteId, 'mode' => $columnName)));
				$chart->setGrid(new f_chart_Grid(0, 20));
				$charts[] = array('chart' => $chart, 'title' => f_Locale::translate("&modules.customer.bo.blocks.dashboardgeneralstatistics.Column-$columnName;"));
			}			
			$request->setAttribute('charts', $charts);
		}
		$request->setAttribute('websites', website_WebsiteService::getInstance()->getAll());
		$request->setAttribute('websiteId', $websiteId);
	}
	
	/**
	 * @param date_Calendar $fromDate
	 * @param date_Calendar $toDate
	 */
	private function initPreviousMonthDates(&$fromDate, &$toDate, $monthCount = 1)
	{
		$fromDate = date_Calendar::now()->sub(date_Calendar::MONTH, $monthCount);
		$fromDate->setDay(1);
		$toDate = date_Calendar::now()->sub(date_Calendar::MONTH, $monthCount);
		$toDate->setDay($toDate->getDaysInMonth());
	}
}
<?php
class ModelCatalogMostRated extends Model {
	public function getMostRatedProducts($limit) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
				
		$product_data_mostrated = $this->cache->get('product.mostrated.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit);

		if (!$product_data_mostrated) { 
			$product_data_mostrated = array();
			
			$query = $this->db->query("SELECT p.`product_id`, COUNT(r.`rating`) AS totrating FROM `" . DB_PREFIX . "review` r, `" . DB_PREFIX . "product` p  WHERE r.`status` = 1 AND p.`product_id` = r.`product_id` AND p.`status` = 1 GROUP BY r.`product_id` ORDER BY totrating DESC LIMIT " . (int)$limit);
			
			foreach ($query->rows as $result) { 		
				$product_data_mostrated[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			
			$this->cache->set('product.mostrated.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data_mostrated);
		}
		
		return $product_data_mostrated;
	}

	public function getTotResults() {						
		$query = $this->db->query("SELECT AVG(r.`rating`) AS totrating, COUNT(r.`rating`) AS totreviews FROM `" . DB_PREFIX . "review` r, `" . DB_PREFIX . "product` p  WHERE r.`status` = 1 AND p.`product_id` = r.`product_id` AND p.`status` = 1" );
		foreach ($query->rows as $result) {  	
			$totrating = $result['totrating'];
			$totreviews = $result['totreviews'];
		}
		return array ($totreviews,$totrating);
	}
}
?>
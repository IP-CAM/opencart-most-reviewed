<?php
class ControllerModuleMostRated extends Controller {
	protected function index($setting) {
		$this->language->load('module/mostrated');

		$this->data['heading_title'] = $this->language->get('heading_title');
				
		$this->data['button_cart'] = $this->language->get('button_cart');
		
		$this->load->model('catalog/product');
		
		$this->load->model('catalog/mostrated');
		
		$this->load->model('tool/image');

		$this->data['products'] = array();
		
		$totrating = 0;
		$totreviews = 0;
		
		$totresults = $this->model_catalog_mostrated->getTotResults();
		$this->data['mostratedaggregate'] = sprintf($this->language->get('text_mostrated'), $totresults[0], round($totresults[1],1));

		$results = $this->model_catalog_mostrated->getMostRatedProducts($setting['limit']);
		
		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
					
			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}	
			
			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}
							
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'reviews'    => sprintf($this->language->get('text_reviews'), $rating, (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/mostrated.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/mostrated.tpl';
		} else {
			$this->template = 'default/template/module/mostrated.tpl';
		}

		$this->render();
	}
}
?>
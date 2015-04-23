<?php
class PDFCatalogModel {
    
    public function getCategories($parent_id = 0) {
		$category_data = array();
	
		$taxonomy     = 'product_cat';
        $orderby      = 'name';  
        $show_count   = 1;      // 1 for yes, 0 for no
        $pad_counts   = 1;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no  
        $title        = '';  
        $empty        = 0;
                
        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'parent'       => (int)$parent_id,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
        );
        $categories = get_categories($args);
        
		foreach ($categories as $category) {
			$category_data[] = array(
				'category_id' => $category->term_id,
				'name'        => $category->name,
                'parents'      => $this->getParent($category->category_parent),
                'product_quantity' => $category->count
			);
		
			$category_data = array_merge($category_data, $this->getCategories($category->term_id));
		}

		return $category_data;
	}
		
	public function getPath($category_id, $rec = true) {
		$query = $wpdb->query("SELECT name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
        return $query->row['name'];
	}
    
    public function getParent($category_id){
        if((int)$category_id >= 1){
            $category = get_term((int)$category_id, 'product_cat' );
    		if ($category->parent) {
    			return $this->getParent($category->parent) . '<span></span>';
    		}else{
                return '<span></span>';
    		}
        }
	}
    
    public function getMinMaxPrice() {
        global $wpdb, $table_prefix;
        $query = $wpdb->get_results("
            SELECT MIN(CAST(meta_value AS DECIMAL(12,2))) AS min, MAX(CAST(meta_value AS DECIMAL(12,2))) AS max
            FROM " . $table_prefix . "postmeta
            WHERE meta_key = '_regular_price'
        ");
        return $query;
    }
}
?>
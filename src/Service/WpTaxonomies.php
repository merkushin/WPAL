<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

final class WpTaxonomies implements Taxonomies {

	/**
	 * @inheritDoc
	 */
	public function create_initial_taxonomies() {
		create_initial_taxonomies();
	}

	/**
	 * @inheritDoc
	 */
	public function get_taxonomies( $args = [], $output = 'names', $operator = 'and' ) {
		return get_taxonomies( $args, $output, $operator );
	}

	/**
	 * @inheritDoc
	 */
	public function get_object_taxonomies( $object, $output = 'names' ) {
		return get_object_taxonomies( $object, $output );
	}

	/**
	 * @inheritDoc
	 */
	public function get_taxonomy( $taxonomy ) {
		return get_taxonomy( $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function taxonomy_exists( $taxonomy ) {
		return taxonomy_exists( $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function is_taxonomy_hierarchical( $taxonomy ) {
		return is_taxonomy_hierarchical( $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function register_taxonomy( $taxonomy, $object_type, $args = [] ) {
		return register_taxonomy( $taxonomy, $object_type, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function unregister_taxonomy( $taxonomy ) {
		return unregister_taxonomy( $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function get_taxonomy_labels( $tax ) {
		return get_taxonomy_labels( $tax );
	}

	/**
	 * @inheritDoc
	 */
	public function register_taxonomy_for_object_type( $taxonomy, $object_type ) {
		return register_taxonomy_for_object_type( $taxonomy, $object_type );
	}

	/**
	 * @inheritDoc
	 */
	public function unregister_taxonomy_for_object_type( $taxonomy, $object_type ) {
		return unregister_taxonomy_for_object_type( $taxonomy, $object_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_objects_in_term( $term_ids, $taxonomies, $args = [] ) {
		return get_objects_in_term( $term_ids, $taxonomies, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_tax_sql( $tax_query, $primary_table, $primary_id_column ) {
		return get_tax_sql( $tax_query, $primary_table, $primary_id_column );
	}

	/**
	 * @inheritDoc
	 */
	public function get_term( $term, $taxonomy = '', $output = 'OBJECT', $filter = 'raw' ) {
		return get_term( $term, $taxonomy, $output, $filter );
	}

	/**
	 * @inheritDoc
	 */
	public function get_term_by( $field, $value, $taxonomy = '', $output = 'OBJECT', $filter = 'raw' ) {
		return get_term_by( $field, $value, $taxonomy, $output, $filter );
	}

	/**
	 * @inheritDoc
	 */
	public function get_term_children( $term_id, $taxonomy ) {
		return get_term_children( $term_id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function get_term_field( $field, $term, $taxonomy = '', $context = 'display' ) {
		return get_term_field( $field, $term, $taxonomy, $context );
	}

	/**
	 * @inheritDoc
	 */
	public function get_term_to_edit( $id, $taxonomy ) {
		return get_term_to_edit( $id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function get_terms( $args = [], $deprecated = '' ) {
		return get_terms( $args, $deprecated );
	}

	/**
	 * @inheritDoc
	 */
	public function add_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
		return add_term_meta( $term_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * @inheritDoc
	 */
	public function delete_term_meta( $term_id, $meta_key, $meta_value = '' ) {
		return delete_term_meta( $term_id, $meta_key, $meta_value );
	}

	/**
	 * @inheritDoc
	 */
	public function get_term_meta( $term_id, $key = '', $single = false ) {
		return get_term_meta( $term_id, $key, $single );
	}

	/**
	 * @inheritDoc
	 */
	public function update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_term_meta( $term_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * @inheritDoc
	 */
	public function update_termmeta_cache( $term_ids ) {
		return update_termmeta_cache( $term_ids );
	}

	/**
	 * @inheritDoc
	 */
	public function has_term_meta( $term_id ) {
		return has_term_meta( $term_id );
	}

	/**
	 * @inheritDoc
	 */
	public function register_term_meta( $taxonomy, $meta_key, array $args ) {
		return register_term_meta( $taxonomy, $meta_key, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function unregister_term_meta( $taxonomy, $meta_key ) {
		return unregister_term_meta( $taxonomy, $meta_key );
	}

	/**
	 * @inheritDoc
	 */
	public function term_exists( $term, $taxonomy = '', $parent = null ) {
		return term_exists( $term, $taxonomy, $parent );
	}

	/**
	 * @inheritDoc
	 */
	public function term_is_ancestor_of( $term1, $term2, $taxonomy ) {
		return term_is_ancestor_of( $term1, $term2, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize_term( $term, $taxonomy, $context = 'display' ) {
		return sanitize_term( $term, $taxonomy, $context );
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize_term_field( $field, $value, $term_id, $taxonomy, $context ) {
		return sanitize_term_field( $field, $value, $term_id, $taxonomy, $context );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_count_terms( $args = [], $deprecated = '' ) {
		return wp_count_terms( $args, $deprecated );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_object_term_relationships( $object_id, $taxonomies ) {
		wp_delete_object_term_relationships( $object_id, $taxonomies );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_term( $term, $taxonomy, $args = [] ) {
		return wp_delete_term( $term, $taxonomy, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_category( $cat_ID ) {
		return wp_delete_category( $cat_ID );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_object_terms( $object_ids, $taxonomies, $args = [] ) {
		return wp_get_object_terms( $object_ids, $taxonomies, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_insert_term( $term, $taxonomy, $args = [] ) {
		return wp_insert_term( $term, $taxonomy, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_set_object_terms( $object_id, $terms, $taxonomy, $append = false ) {
		return wp_set_object_terms( $object_id, $terms, $taxonomy, $append );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_add_object_terms( $object_id, $terms, $taxonomy ) {
		return wp_add_object_terms( $object_id, $terms, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_remove_object_terms( $object_id, $terms, $taxonomy ) {
		return wp_remove_object_terms( $object_id, $terms, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_unique_term_slug( $slug, $term ) {
		return wp_unique_term_slug( $slug, $term );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_term( $term_id, $taxonomy, $args = [] ) {
		return wp_update_term( $term_id, $taxonomy, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_defer_term_counting( $defer = null ) {
		return wp_defer_term_counting( $defer );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_term_count( $terms, $taxonomy, $do_deferred = false ) {
		return wp_update_term_count( $terms, $taxonomy, $do_deferred );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_term_count_now( $terms, $taxonomy ) {
		return wp_update_term_count_now( $terms, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function clean_object_term_cache( $object_ids, $object_type ) {
		return clean_object_term_cache( $object_ids, $object_type );
	}

	/**
	 * @inheritDoc
	 */
	public function clean_term_cache( $ids, $taxonomy = '', $clean_taxonomy = true ) {
		return clean_term_cache( $ids, $taxonomy, $clean_taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function clean_taxonomy_cache( $taxonomy ) {
		clean_taxonomy_cache( $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function get_object_term_cache( $id, $taxonomy ) {
		return get_object_term_cache( $id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function update_object_term_cache( $object_ids, $object_type ) {
		return update_object_term_cache( $object_ids, $object_type );
	}

	/**
	 * @inheritDoc
	 */
	public function update_term_cache( $terms, $taxonomy = '' ) {
		update_term_cache( $terms, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function _update_generic_term_count( $terms, $taxonomy ) {
		_update_generic_term_count( $terms, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function _split_shared_term( $term_id, $term_taxonomy_id, $record = true ) {
		return _split_shared_term( $term_id, $term_taxonomy_id, $record );
	}

	/**
	 * @inheritDoc
	 */
	public function _wp_batch_split_terms() {
		_wp_batch_split_terms();
	}

	/**
	 * @inheritDoc
	 */
	public function _wp_check_for_scheduled_split_terms() {
		_wp_check_for_scheduled_split_terms();
	}

	/**
	 * @inheritDoc
	 */
	public function _wp_check_split_default_terms( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
		_wp_check_split_default_terms( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function _wp_check_split_terms_in_menus( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
		_wp_check_split_terms_in_menus( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function _wp_check_split_nav_menu_terms( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
		_wp_check_split_nav_menu_terms( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_split_terms( $old_term_id ) {
		return wp_get_split_terms( $old_term_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_split_term( $old_term_id, $taxonomy ) {
		return wp_get_split_term( $old_term_id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_term_is_shared( $term_id ) {
		return wp_term_is_shared( $term_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_term_link( $term, $taxonomy = '' ) {
		return get_term_link( $term, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function the_taxonomies( $args = [] ) {
		return the_taxonomies( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_the_taxonomies( $post = 0, $args = [] ) {
		return get_the_taxonomies( $post, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_taxonomies( $post = 0 ) {
		return get_post_taxonomies( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function is_object_in_term( $object_id, $taxonomy, $terms = null ) {
		return is_object_in_term( $object_id, $taxonomy, $terms );
	}

	/**
	 * @inheritDoc
	 */
	public function is_object_in_taxonomy( $object_type, $taxonomy ) {
		return is_object_in_taxonomy( $object_type, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function get_ancestors( $object_id = 0, $object_type = '', $resource_type = '' ) {
		return get_ancestors( $object_id, $object_type, $resource_type );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_term_taxonomy_parent_id( $term_id, $taxonomy ) {
		return wp_get_term_taxonomy_parent_id( $term_id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_term_hierarchy_for_loops( $parent, $term_id, $taxonomy ) {
		return wp_check_term_hierarchy_for_loops( $parent, $term_id, $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function is_taxonomy_viewable( $taxonomy ) {
		return is_taxonomy_viewable( $taxonomy );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_cache_set_terms_last_changed() {
		wp_cache_set_terms_last_changed();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_term_meta_support_prefilter( $check ) {
		return wp_check_term_meta_support_prefilter( $check );
	}
}

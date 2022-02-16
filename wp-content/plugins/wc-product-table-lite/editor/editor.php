<?php
  if ( ! defined( 'ABSPATH' ) ) {
  	exit; // Exit if accessed directly
  }
?>

<div class="wcpt-editor-clear"></div>

<h1 class="wcpt-page-title dashicons-before dashicons-editor-justify">
  <?php _e( "WooCommerce Product Table", "wc-product-table" ); ?>
</h1>

<div class="wcpt-title-resources">
  <a href="https://wcproducttable.com/tutorials/" target="_blank">How to use</a>
  /
  <a href="https://wcproducttable.com/documentation/" target="_blank">Documentation</a>
  /
  <a href="https://www.notion.so/FAQs-f624e13d0d274a08ba176a98d6d79e1f" target="_blank">FAQs</a>    
  /
  <a href="https://wcproducttable.com/support/" target="_blank">Support</a>
</div>

<div class="wcpt-editor-clear"></div>

<!-- presets -->
<?php 
if( wcpt_preset__required() ){
  echo wcpt_presets__get_grid_markup();
  return;
}
?>

<div class="wcpt-editor-clear"></div>

<!-- top options -->
<span class="wcpt-table-title-label"><?php _e( "Table name", "wc-product-table" ); ?>:</span>
<input type="text" class="wcpt-table-title" name="" value="<?php echo (isset($_GET['post_id']) ? get_the_title( (int) $_GET['post_id'] ) : ''); ?>" placeholder="Enter name here..." />
<br>
<span class="wcpt-sc-display-label"><?php _e( "Shortcode", "wc-product-table" ); ?>:</span>
<input class="wcpt-sc-display" value="<?php esc_html_e( '[product_table id="'. $post_id .'"]' ); ?>" onClick="this.setSelectionRange(0, this.value.length)" readonly />
<span class="wcpt-shortcode-info wcpt-toggle wcpt-toggle-off">
  <span class="wcpt-toggle-trigger wcpt-noselect">
    <?php echo wcpt_icon('chevron-down', 'wcpt-toggle-is-off'); ?>
    <?php echo wcpt_icon('chevron-up', 'wcpt-toggle-is-on'); ?>
    Shortcode Options
    <?php echo wcpt_icon('sliders'); ?>
  </span>
  <span class="wcpt-toggle-tray">

    <?php echo wcpt_icon('x', 'wcpt-toggle-x'); ?>

    <table>
       <thead>
          <tr>
             <td><strong>Attribute</strong></td>
             <td><strong>Description</strong></td>
          </tr>
       </thead>
       <tbody>
          <tr>
             <td>name</td>
             <td>[product_table name="test table"] <br>Can be used to replace id attribute.</td>
          </tr>
          <tr>
             <td>offset</td>
             <td>[product_table id="123" offset="6"] <br>Number of initial products to skip over. In this example the shortcode will skip the first 6 products.</td>
          </tr>
          <tr>
             <td>limit</td>
             <td>[product_table id="123" limit="8"] <br>Limits the number of products per page.</td>
          </tr>
          <tr>
             <td>category</td>
             <td>[product_table id="123" category="clothes, shoes"] <br>
             Enter comma separated category slugs. This will change the product categories displayed by the table. If the category spellings are incorrect or the categories do not exist on the site no results will be displayed. </td>
          </tr>
          <tr>
             <td>exclude_category <br><?php wcpt_pro_badge(); ?></td>
             <td>[product_table id="123" exclude_category="clothes, shoes"] <br>
             Enter comma separated category slugs. This will exclude the specified product categories from the table</td>
          </tr>
          <tr>
             <td>cat_operator <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" cat_operator="AND"] <br>
                AND – Will display products that belong in all of the chosen categories. <br>
                IN – Will display products within the chosen category. This is the default cat_operator value. <br>
                NOT IN – Will display products that are not in the chosen category. <br>
             </td>
          </tr>
          <tr>
             <td>nav_category<br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" nav_category="clothes, shoes"] <br>
               You can use this attribute to narrow down the category options that will appear in the table navigation > category filter. For example, if you have selected categories Clothes, Shoes, and Accessories in your table settings, you can enter slugs nav_category="clothes, shoes" to only show these category options. Alternatively you can also use nav_category_id="123, 124, 125" to select categories using term taxonomy id. 
            </td>
          </tr>
          <tr>
             <td>ids</td>
             <td>[product_table id="123" ids="100, 101, 102"] <br>Enter comma separated product IDs to limit the table results to those products.</td>
          </tr>

          <tr>
             <td>exclude_ids <br><?php wcpt_pro_badge(); ?></td>
             <td>[product_table id="123" exclude_ids="100, 101, 102"] <br>Enter comma separated product IDs to exclude those products from the table.</td>
          </tr>

          <tr>
             <td>skus</td>
             <td>[product_table id="123" skus="sku1, sku2"] <br>Enter comma separated product SKUs to limit the table results to those specific products.</td>
          </tr>

          <tr>
             <td>variation_skus <br><?php wcpt_pro_badge(); ?></td>
             <td>[product_table id="123" product_variations="true" variation_skus="sku1, sku2"] <br>Comma separated product variation SKUs. Requires product_variations="true" to ensure the shortcode is printing a product variation table.</td>
          </tr>

          <tr>
             <td>include_hidden <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" include_hidden="true"] <br>
               Include products that are hidden from shop / search page as per their product settings.
             </td>
          </tr>
          <tr>
             <td>include_private <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" include_private="true"] <br>
               Include products with private status which are normally hidden from site visitors.
             </td>
          </tr>
          <tr>
             <td>attribute <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" attribute="Attribute 1: Term 1, Term 2 | Attribute 2: Term 3, Term 4"] <br>
               Pre-selects attributes for the table. Pattern: attribute name/slug, followed by ':', then one or more attribute term names or slugs separaed by comma. Then a bang '|' followed by next attribute-slug:term-slug combination and so on. Requires global level attributes.
             </td>
          </tr>
          <tr>
             <td>exclude_attribute <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" exclude_attribute="Attribute 1: Term 1, Term 2 | Attribute 2: Term 3, Term 4"] <br>
               Removes products with specified attribute terms from the results. Pattern: attribute name/slug, followed by ':', then one or more attribute term names or slugs separaed by comma. Then a bang '|' followed by next attribute-slug:term-slug combination and so on. Only work with global attributes, not custom attributes. The excluded attribute terms will also be removed from the table's navigation filter Attribute options.
             </td>
          </tr>
          <tr>
             <td>attribute_relation <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" attribute_relation="OR"] <br>
               The default relation between attributes for filtering results is 'AND', which means, a product will be included in the filtering results only if it satisfies all the attribute filters. You can use 'OR' to include products that satisfy atleast one of the filters. 
             </td>
          </tr>
          <tr>
             <td>custom_field <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" custom_field="CF 1: Val 1, Val 2 | CF 2: Val 3, Val 4"] <br>
               Use this to set custom field values for the table. Pattern: custom field key, followed by ':', then one or more comma separated values that you want to permit for that custom field. To enter rules for more custom fields, enter a bang '|' followed by next custom field key and its values and so on. Other possible values are:<br> 
               "CF 1: *NOT EXISTS*": only shows products that don't have CF 1 set on them<br> 
               "CF 1: *EXISTS*": only shows products that do have CF 1 set on them<br> 
               "CF 1: *BETWEEN* 1, 9": only shows products with CF 1 value between 0 and 9<br> 
               "CF1: *NOT IN* red, blue, green": Only shows products that don't have CF 1 set to values red, blue or green
               "CF1: *LIKE* red": Shows products that have CF 1 value like 'red'
             </td>
          </tr>
          <tr>
             <td>tags <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" tags="tag1, tag2"] <br>
               Show products related to specific tags. Enter comma separated tag slugs as the value.
             </td>
          </tr>
          <tr>
             <td>tags_operator <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" tags="tag1, tag2" tags_operator="AND"] <br>
               IN (default) – This will display products that have either tag1 or tag2.<br>         
               AND – This will display products that have both tag1 and tag2. <br>
               NOT IN – This will display products that don't have tag1 or tag2. <br>
             </td>
          </tr>
          <tr>
             <td>taxonomy <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" taxonomy="tax-1: Term 1, Term 2 | tax-2: Term 3, Term 4"] <br>
               Pre-selects taxonomy for the table. Pattern: taxonomy slug, followed by ':', then one or more permitted terms. Then a bang '|' followed by next taxonomy and so on.
             </td>
          </tr>

          <tr>
             <td>min_price /<br> max_price <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" min_price="10" max_price="100"] <br>
               Pre-selects the price range for products in the table. You can use either or both the attributes in the shortcode.
             </td>
          </tr>

          <tr>
             <td>on_sale <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" on_sale="true"] <br>
               Only shows on sale product in the table. Ensure no category is selected unless you want to only show on sale products from that category.
             </td>
          </tr>

          <tr>
             <td>out_of_stock <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" out_of_stock="true"] <br>
               Only shows out of stock products in the table.
             </td>
          </tr>

          <tr>
             <td>include_out_of_stock <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" include_out_of_stock="true"] <br>
               Includes out of stock products in the results, overriding table settings.
             </td>
          </tr>          

          <!-- <tr>
             <td>one_page_instant_search <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" one_page_instant_search="true"] <br>
               Overrides the search navigation filter to perform client side filtering. Useful when you are displaying only one page of results in the table.<br> 
               Please note limitation:<br>
               1. Only searches in products that are already loaded on the browser page. <br>
               2. Only searches through the text that is printed in the table product rows. <br>
               3. Ignores all search match weightage rules. Performs simple search. <br>
               4. Can be slow in case of large number of products on page. <br>
               5. This facility will not work with fixed columns or heading
             </td>
          </tr>   

          <tr>
             <td>one_page_instant_sort_columns <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" one_page_instant_sort_columns="2, 3, 5"] <br>
               Provides client side column sorting. Useful when you are displaying only one page of results in the table.<br> 
               Please note limitation:<br>
               1. Sorts based on text printed in the columns. Ignores any 'Sorting' element settings for the column. <br>
               2. Only sorts the product rows that are currently printed in the table.<br>
               3. Does not sync with the Sort By navigation element. <br>
               4. This facility will not work with fixed columns or heading
             </td>
          </tr>            -->

          <tr>
             <td>exclude_out_of_stock <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" exclude_out_of_stock="true"] <br>
               Excludes products that are out of stock.
             </td>
          </tr>

          <tr>
             <td>featured <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" featured="true"] <br>
               Only shows featured product in the table. Ensure no category is selected unless you want to only show featured products from that category.
             </td>
          </tr>

          <tr>
             <td>product_type <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" product_type="simple, variable"] <br>
               Only shows specific product types in the table results. You need to enter comma separated product type slugs.
             </td>
          </tr>          

          <tr>
             <td>exclude_product_type <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" exclude_product_type="simple, variable"] <br>
               Excludes specific product types from the table results. You need to enter comma separated product type slugs.
             </td>
          </tr>          

          <tr>
             <td>no_results_message <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" no_results_message="No results found!"] <br>
               [product_table id="123" no_results_message="page_id: 123"] <br>
               [product_table id="123" no_results_message="option: option_name"] <br>
               [product_table id="123" no_results_message="*empty*"] <br>
               You can already enter a custom 'no results' message from wp admin > product tables > settings > no results.<br>
               But the no_results_message="..." shortcode attribute goes a step further and helps you set unique no results message based on shortcode. <br>
               To get the message from a wordpress page enter value "page_id: 123" (replace 123 with page id).<br>
               To get the message from a wordpress option enter value "option: option_name" (replace option_name).<br>
               To remove the message enter "*empty*" as the value.<br>
               To provide a translation in French (or any language) enter no_results_message_fr_fr="french message" (replace fr_fr with the locale code of the language you are targeting).
             </td>
          </tr>

          <tr>
             <td>show_upsells /<br> show_cross_sells /<br> show_related_products <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" show_related_products="true"] <br>
               You can show upsell, cross sell and related products using these shortcode attributes. Set the value as a product ID, or if the shortcode is called on a single product page just use "true" and WCPT will use the current product's ID.  
             </td>
          </tr>

          <tr>
             <td>enable_visibility_rules <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" enable_visibility_rules="true"] <br>
               Useful for integrating with 3rd party plugins that hide / show products based on user role and other visibility criteria.
             </td>
          </tr>

          <tr>
             <td>upcoming <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" upcoming="show | hide"] <br>
               Useful for showing or hiding upcoming products when you are using the <a href="https://wordpress.org/plugins/woocommerce-upcoming-product/" target="_blank">woocommerce upcoming products</a> plugin.
             </td>
          </tr>

          <tr>
             <td>form_mode <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" form_mode="true"] <br>
               This facility is useful when you have replaced the product grid on your shop page with a product table using the <a href="https://wcproducttable.com/documentation/enable-archive-override" target="_blank">archive override facility</a>, and now you wish to provide a form on another page where users can select filter values and are then redirected to the shop page where those filters are applied on the table. <br>
               Using this sc attribute will hide the product results from the table, leaving just the navigation section visible. To complete the form look, you can also add the 'Apply / Reset' element in the table navigation section. <br> 
               Please remember that the filters in the form should be a subset of the filters on the shop page. If the site visitor selects filters in the form that are not available on the shop page, then those filters will not be applied to the results on the shop page.
             </td>
          </tr>

          <tr>
             <td>open_dropdown_on_click <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" open_dropdown_on_click="true"] <br>
                This makes navigation filter dropdowns open only when they are clicked instead of opening on mouse hover.
             </td>
          </tr>

          <!-- <tr>
             <td>enable_dropdown_hover_intent <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" enable_dropdown_hover_intent="true"] <br>
                Provide a smoother UI by avoiding chances of navigation dropdown accidentaly opening as user hovers across it while they are trying to reach another element.
             </td>
          </tr> -->

          <tr>
             <td>category_required <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" category_required="true" category_required_message="Select category"] <br>
               Use this when you want to hide results until visitor has selected a product category. <br>
               You can also use category_required_message if you want to customize the message that appears when user has not selected any category.
             </td>
          </tr>

          <tr>
             <td>attribute_required <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" attribute_required="Color, Size" attribute_required_message="Select {attribute}"] <br>
               Use this when you want to hide results until visitor has selected specific attributes. You must enter the attribute names in the value.<br>
               You can also use attribute_required_message if you want to customize the message that appears when user has not selected required attributes. Use {attribute} in the message as a placeholder for the attribute name.
             </td>
          </tr>       

          <tr>
             <td>filter_required <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" filter_required="true" filter_required_message="Please select category, color or search to show results"] <br>
               Use this when you want to hide results until visitor has selected a category or attribute or else used search.<br>
               You can also use filter_required_message if you want to customize the message that appears when user has not selected any filter.
             </td>
          </tr>       


          <tr>
             <td>orderby <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" orderby="skus" skus="woo-123, woo-127, woo-122, woo-128"] <br>
               Use this when you want to order products in the same sequence as the skus or ids you have entered via the shortcode attributes ids="..." and skus="...". Only accepted values are orderby="skus" and orderby="ids".
             </td>
          </tr>

          <tr>
             <td>secondary_orderby <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" secondary_orderby="title" secondary_order="ASC"] <br>
               The options 'secondary_orderby' and 'secondary_order' let you add a second sorting condition. This is useful, for example, when you are sorting products by price, but want products with the same price to be sorted by title. <br>
               The permitted values for 'secondary_orderby' are: title, date, menu_order, price, custom_field_number, custom_field_text, ID, SKU_number and SKU_text. <br> 
               Permitted values for 'secondary_order' are - ASC and DESC. <br>
               If you are setting secondary_orderby to custom_field_number or custom_field_text then you need to also enter secondary_custom_field="*name of custom field*".<br>
               This faciity cannot be combined with product variations table (product_variations="true").
             </td>
          </tr>

          <tr>
             <td>use_default_search</td>
             <td>
               [product_table id="123" use_default_search="true"] <br>
               Use this to disable the WCPT search rules and switch to default woocommerce search instead in the table. 
             </td>
          </tr>          

          <tr>
             <td>search_orderby <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" search_orderby="2"] <br>
               When search is conducted, you can make the results order by any of the 'Sort by' filter options. Simply enter the 'Sort by' filter option number in search_orderby.
             </td>
          </tr>

          <tr>
            <td>
              laptop_child_row_columns <br>
              tablet_child_row_columns <br>
              phone_child_row_columns <br>
              <?php wcpt_pro_badge(); ?>
            </td>
            <td>
               [product_table id="123" laptop_child_row_columns="sku: 100% | price: 50% | color: 50%"] <br>
               A toggle button is added to the start of the row. Child row columns will be hidden until the toggle button is clicked. Please note: you need to name your columns in the editor to use this feature. <br>
              Other related options:<br>
              child_row_toggle_icon_color="#000" <br>
              child_row_toggle_icon_background_color="#fff" <br>
            </td>
          </tr>          

          <tr>
             <td>show_for_user_role / hide_for_user_role<br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" show_for_user_role="guest | administrator | shop_manager"] <br>
               Enter user role slugs separated by pound "|".
             </td>
          </tr>
          
          <tr>
             <td>refresh_table <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" refresh_table="true"] <br>
               This will lock up the table whenever an add to cart / remove from cart action takes place and refresh its view when the action is completed. The purpose is to refresh prices and stocks to keep them always in line with any special pricing rulee applied via custom code or 3rd party plugin.
             </td>
          </tr>

          <tr>
             <td>block_table <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" block_table="true"] <br>
               This will lock up the table while any add to cart / remove from cart operations are taking place.
             </td>
          </tr>
          
          <tr>
             <td>
              laptop_auto_scroll <br>
              tablet_auto_scroll <br>
              phone_auto_scroll <br>
             </td>
             <td>
              [product_table id="123" laptop_auto_scroll="true"] <br>
              [product_table id="123" tablet_auto_scroll="true"] <br>
              [product_table id="123" phone_auto_scroll="true"] <br>
              When you are using this, each time your site visitor refreshes the table over AJAX (by filtering, changing table page, searching or sorting) WCPT will automatically scroll the page to bring the top of the table container into view for the convenience of the site visitor.
             </td>
          </tr>

          <tr>
             <td>
              laptop_scroll_offset <br>
              tablet_scroll_offset <br>
              phone_scroll_offset
             </td>
             <td>
              [product_table id="123" laptop_scroll_offset="100"] <br>
              [product_table id="123" tablet_scroll_offset="100"] <br>
              [product_table id="123" phone_scroll_offset="100"] <br>
              This is related to the 'auto scroll' facility mentioned above. If you have a floating mega menu or other fixed items at the top of the page, they can obstruct the table view after auto scroll by appearing infront of the top of the table. To solve this, you can push the table further down by using laptop_scroll_offset="100" <br>
              Replace 100 with a figure similar to the height of the element fixed at the top of your page.<br> 
              This property also controls the offset for the 'freeze heading' facility. <br>
              Instead of a number you can also enter the CSS selector for the fixed element eg "#wpadminbar".
             </td>
          </tr>

          <tr>
             <td>
               laptop_freeze_left <br>
               laptop_freeze_right <br><br>

               tablet_freeze_left <br>
               tablet_freeze_right <br><br>

               phone_freeze_left <br>
               phone_freeze_right <br>
               <?php wcpt_pro_badge(); ?>
             </td>
             <td>
             [product_table id="123" laptop_freeze_left="1" laptop_freeze_right="1"]<br>
             Use this facility to freeze columns on the left and right of your table while allowing it to scroll horizontally. Similar to what you can do in an Excel file.<br>
             Note:<br>
             Please keep in mind this facility is highly resource intensive on the client device (visitor's browsing device). Therefore avoid using it on tables with a very large number of products, and conduct adequate performance testing. <br>
             You can also use grab_and_scroll="true" to enable horizontal scrolling by holding the mouse down on the table and scrolling it left and right.
             </td>
          </tr>

          <tr>
            <td>
              laptop_freeze_heading <br>            
              tablet_freeze_heading <br>
              phone_freeze_heading <br>
              <?php wcpt_pro_badge(); ?>
            </td>
             <td>[product_table id="123" laptop_freeze_heading="true"]<br>
             You can freeze the table heading at the top of the screen using this facility, making it convenient for the site visitor to keep track of the columns on large tables.<br>
             If another element on your site is already fixed at the top of the screen, like a mega menu, you might find the fixed table heading getting hidden behind it. In such a case you can shift the fixed table heading further down by using the laptop_scroll_offset facility that is covered above.
             </td>
          </tr>

          <tr>
             <td>json_ld <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" json_ld="true"] <br>
               Prints JSON-LD for the product table shortcode. On archive pages the JSON-LD is printed automatically.
             </td>
          </tr>
          <tr>
             <td>
              lazy_load<br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" lazy_load="true"] <br>Speed up page load by lazy loading the table.</td>
          </tr>
          <tr>
             <td>
              store / store_id<br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" store="Store Username" store_id="5"] <br>If you are using WCFM marketplace plugin to create a marketplace site, you can use either store or store_id to diplay products from a specific store. The store username is in wp admin > users > all users > Store > Username. Please check the <a href="https://www.notion.so/FAQs-f624e13d0d274a08ba176a98d6d79e1f" target="_blank">FAQs</a> for questions related to WCFM. </td>
          </tr>
          <tr>
             <td>
              author_username / vendor<br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>
             [product_table id="123" author_username="username"] <br>
             [product_table id="123" vendor="vendor_username"] <br>
             If you are using a marketplace plugin like Dokan where vendors are actually wordpress users, then you can use this shortcode attribute to diplay products created by a specific user or belonging to a vendor. You need to use the single word, lowercase, wordpress username of the author / vendor (not "Firstname Lastname").</td>
          </tr>          
          <tr>
             <td>
              product_variations <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" product_variations="true" ids="111"] <br>Please see <a href="https://wcproducttable.com/documentation/variations-table" target="_blank">docs</a>. Lets you display the product variations of a specific variable product in a table in separate rows. Other product types like 'simple' products are excluded. It is useful when you need to show variations of a product in a table. Enter the variable product id in shortcode attribute 'ids'.</td>
          </tr>
          <tr>
             <td>
              laptop_attribute_columns <br>
              phone_attribute_columns <br>
              tablet_attribute_columns <br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" product_variations="true" laptop_attribute_columns="2: pa_color | 3: size | 6: pa_logo "] <br>Please see <a href="https://wcproducttable.com/documentation/variations-table" target="_blank">docs</a>. This is used in conjunction with product_variations="true" to only show the columns that are relevant to the current variable product. This can save you the effort of creating multiple tables for variable products with different attributes.</td>
          </tr>
          <tr>
             <td>
              hide_empty_columns <br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" hide_empty_columns="true"] <br>Use this to hide columns that have no output in them. Please note, that this will only work if none of the cells in a column is printing anything. If you have attribute or custom field elements in the column that are configured to show a default output when there is no value, then the column will still be shown even if there is no attribute term or custom field to print from the element.</td>
          </tr>
          <tr>
             <td>
              laptop_hide_columns <br>
              phone_hide_columns <br>
              tablet_hide_columns <br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" laptop_hide_columns="2, 4" phone_hide_columns="1"] <br>This facility is useful when you want to create a 'master' table with all the columns and then hide specific columns from the table while printing it on different pages. This helps you greately reduce the work inolved in creating tables on your site. You could create a single master table and this using its shortcode and this facility you can create other tables from it by hide columns irrelevant to that table.</td>
          </tr>
          <tr>
              <td>
              grouped_product_ids <br>
              <?php wcpt_pro_badge(); ?>
              </td>
              <td>[product_table id="123" grouped_product_ids="456, 457"] <br>
              Enter comma separated grouped product ids to displays their child products in the table.</td>
          </tr>
          <tr>
             <td>
             show_previous_orders <br>
             hide_previous_orders <br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" show_previous_orders="true"] <br>Use this to only show products that have been previously ordered by the customer. The table will not be displayed if the user has not purchased any products from the shop in the past. In the value, instead of "true" you can enter comma separated list of order status to permit. For example "completed, processing". Permitted values: pending, processing, on-hold, completed, cancelled, refunded, failed. Default values are completed and processing.
            </td>
          </tr>
          <tr>
             <td>
              show_recently_viewed <br>
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" show_recently_viewed="true"] <br>This facility will only work if you already have the woocommerce recently viewed widget enabled on your site. So please ensure you enable that widget first. If there are no results in the table, ie, customer has not viewed any products yet, the table will simply hide itself. You can also use the CSS .widget_recently_viewed_products {display: none !important;} to hide the recently viewed widget after enabling it on your site. </td>
          </tr>
          <tr>
            <td>
              hide_empty_table <br>
              <?php wcpt_pro_badge(); ?>
            </td>
            <td>[product_table id="123" hide_empty_table="true"] <br>If the table has no results when it is first loaded, it will be hidden from view. This helps avoid unnecessarily showing completely blank tables on the screen. This does not affect tables that show no results after customer has used the navigation filters. </td>
          </tr>
          <tr>
             <td>quick_view_trigger <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="123" quick_view_trigger="title, product image"] <br>
               If you are using a compatible quick view plugin (see <a href="https://www.notion.so/List-of-3rd-party-plugins-compatible-with-WCPT-PRO-b6138e2590684cf49a198beb292aa3c1" target="_blank">compatibility list</a>) then you can make your title or product image trigger the quick view upon click.<br> Also, you can use the following shortcode attributes to set conditions for which products get the quick view trigger:<br>
                quick_view_category="slug-1, slug-2"<br>
                quick_view_exclude_category="slug-3, slug-4",<br>
                quick_view_product_type="simple, variable",<br>
                quick_view_exclude_product_type="grouped, external", 
             </td>
          </tr>
          <tr>
            <td>custom_attribute <br><?php wcpt_pro_badge(); ?></td>
            <td>
              [product_table id="123" product_variations="true" ids="456" custom_attribute="Attribute 1: Term 1, Term 2 | Attribute 2: Term 3, Term 4"] <br>
              Select product variations based on custom attribute terms. This facility only works with product variations. WooCommerce does not allow filtering other product types by custom attributes due to the way it stores information in the database. Therefore, in the case of other product types you need to use global attributes for filtering.
            </td>
          </tr>
          <tr>
             <td>
               dynamic_hide_filters <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" dynamic_hide_filters="true"] <br> Dynamically hides category, attribute, availability, on sale, tag and taxonomy filter options that are not relevant to the current result set. <br>Note: (1) Highly resource intensive facility! Only use on relatively small number of products. Disable it if loading slows down. (2) Not compatible with product variation table product_variations="true"</td>
          </tr>
          <tr>
             <td>
              dynamic_recount <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" dynamic_recount="true"] <br> Dynamically recounts the number of items relevant to category, attribute, availability, on sale, tag and taxonomy filter options, and prints the figure next to each option. <br>Note: Highly resource intensive facility! Only use on relatively small number of products. Disable it if loading slows down. (2) Not compatible with product variation table product_variations="true"</td>
          </tr>

          <tr>
             <td>
               dynamic_filters_lazy_load <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="123" dynamic_hide_filters="true" dynamic_filters_lazy_load="true"] <br> This facility can be useful only in specific cases where there are several options in the navigation filters that need to be run through dynamic filters. Their processing is defered to a second AJAX call. But only use it if the product results were loading slow. Using it <em>unnecesarily</em> will only create extra AJAX calls and slow down results.</td>
          </tr>

          <tr>
             <td>
             checked_row_background_color <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>
               [product_table id="123" checked_row_background_color="yellow"] <br>
                Use this to give a unique background color to the checked rows, making it easier for the customer to spot them. You need to add the Checkbox element to a table column first. 
            </td>
          </tr>          

          <tr>
             <td>
               ti_wishlist <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>
             [product_table id="123" ti_wishlist="true"] <br> 
             This requires the free version of the <a href="https://wordpress.org/plugins/ti-woocommerce-wishlist/"  target="_blank">TI WooCommerce Wishlist</a> plugin installed and configured on your site. Curently this integration only works with the free version of TI Wishlist.
             <br>             
             Use this facility to print a WCPT table with the customer's wishlist items in it. Also create a Shortcode element in your product table and enter in it the [wcpt_wishlist] shortcode to provide the wishlist button. Check the Shortcode element for the fill parameter list for the [wcpt_wishlist] shortcode.
             </td>
          </tr>

          <tr>
             <td>
              disable_ajax
             <td>[product_table id="123" disable_ajax="true"] <br>This can be useful when 3rd party plugin elements are being displayed inside WCPT and require full page reload upon filtering and pagination to show up correctly.</td>
          </tr>
          <tr>
             <td>
              disable_url_update
             </td>
             <td>[product_table id="123" disable_url_update="true"] <br>Prevents the browser url from continously changing when the WCPT navgiation filters are used.</td>
          </tr>

          <tr>
             <td>
              html_class
             </td>
             <td>[product_table id="123" html_class="special-table"] <br>Insert an additional HTML class to the wrapper.</td>
          </tr>          
       </tbody>
    </table>

  </span>
</span>

<!-- table creation checklist / preset applied message -->
<?php 
  if( ! wcpt_preset__maybe_display_message() ){
    require_once('partials/checklist.php');
  }
?>

<!-- editor begins -->
<div class="wcpt-editor" wcpt-model-key="data">
  <!-- tab triggers -->
  <div class="wcpt-tab-label wcpt-products-tab active" data-wcpt-tab="products">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('box', 'wcpt-tab-label-icon'); ?>
      <?php _e( "Query", "wc-product-table" ); ?>
    </span>
  </div>
  <div class="wcpt-tab-label wcpt-columns-tab" data-wcpt-tab="columns">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('menu', 'wcpt-tab-label-icon wcpt-rotate-90'); ?>
      <?php _e( "Columns", "wc-product-table" ); ?>
    </span>
  </div>
  <div class="wcpt-tab-label wcpt-navigation-tab" data-wcpt-tab="navigation">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('filter', 'wcpt-tab-label-icon'); ?>
      <?php _e( "Navigation", "wc-product-table" ); ?>
    </span>
  </div>
  <div class="wcpt-tab-label wcpt-style-tab " data-wcpt-tab="style">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('type', 'wcpt-tab-label-icon'); ?>
      <?php _e( "Style", "wc-product-table" ); ?>
    </span>
  </div>

  <!-- products tab -->
  <div class="wcpt-editor-tab-products  wcpt-tab-content active" data-wcpt-tab="products" wcpt-model-key="query">
    <?php require_once('partials/query.php') ?>
  </div>

  <!-- columns tab -->
  <div class="wcpt-editor-tab-columns wcpt-tab-content" data-wcpt-tab="columns" wcpt-model-key="columns">

    <?php
      // create the 3 device columns ui
      foreach( array('laptop', 'tablet', 'phone') as $device ){
        ?>
        <!-- <?php echo $device ?> -->
        <div
          class="wcpt-editor-columns-container wcpt-sortable"
          data-wcpt-device="<?php echo $device; ?>"
          wcpt-model-key="<?php echo $device; ?>"
          wcpt-connect-with="[wcpt-controller='device_columns']"
          wcpt-controller="device_columns"
        >
          <h2 class="wcpt-editor-light-heading">
            <?php
              $device_icon = array( 'laptop'=> 'square', 'tablet'=> 'tablet', 'phone'=> 'smartphone' );
              $src = WCPT_PLUGIN_URL . '/assets/feather/' . $device_icon[$device] . '.svg';
            ?>
            <img 
              class="wcpt-column-device-icon wcpt-column-device-icon--<?php echo $device; ?>" 
              src="<?php echo $src; ?>" 
            />
            <span><?php echo ucfirst( $device ); ?> Columns: </span>
            <div class="wcpt-column-links"></div>
            <div class="wcpt-device-columns-toggle">
              <a href="#" class="wcpt-device-columns-toggle__expand">Expand</a> /
              <a href="#" class="wcpt-device-columns-toggle__contract">Contract</a>
              all
            </div>            
          </h2>
          <?php require('partials/columns.php'); ?>
        </div>
        <hr class="wcpt-editor-columns-device-divider">
        <?php
      }
    ?>

  </div><!-- /columns tab -->

  <!-- style tab -->
  <div class="wcpt-editor-tab-style wcpt-tab-content" data-wcpt-tab="style" wcpt-model-key="style">
    <?php require_once('partials/style.php') ?>
  </div>

  <!-- navigation tab -->
  <div class="wcpt-editor-tab-navigation wcpt-tab-content" data-wcpt-tab="navigation" wcpt-model-key="navigation" wcpt-initial-data="navigation">
    <?php require_once('partials/navigation.php') ?>
  </div>

</div><!-- /.wcpt-editor -->

<!-- save data -->
<div class="wcpt-editor-save-table-clear"></div>
<div class="wcpt-editor-save-table">
  <form class="wcpt-save-data" action="wcpt_save_table_settings" method="post">
    <!-- hidden fields -->
    <input name="post_id" type="hidden" value="<?php echo $post_id; ?>" />
    <input name="nonce" type="hidden" value="<?php echo wp_create_nonce( "wcpt" ); ?>">
    <input name="title" type="hidden" value="<?php echo ( isset($post_id) ? get_the_title( $post_id ) : __( "Untitled table", "wc-product-table" ) ); ?>" />
    <button type="submit" class="wcpt-editor-save-button button button-primary button-large"><?php _e( "Save settings", "wcpt" ); ?></button>
    <i class="wcpt-saving-icon">
      <?php wcpt_icon('loader', 'wcpt-rotate'); ?>
    </i>
    <br/>
    <div class="wcpt-save-keys">
      Press "Ctrl/Cmd + s" to save!
    </div>
    <div class="wcpt-editor__saving">
      <?php wcpt_icon('loader', 'wcpt-rotate'); ?>
      <span>Saving...</span>
    </div>      
  </form>
</div>

<div class="wcpt-footer">
  <div class="wcpt-support wcpt-footer-note">
    <?php wcpt_icon('alert-circle'); ?>
    <span><?php _e( "Found a bug / Got questions? Please reach out for support here: ", "wc-product-table" ); ?><a href="mailto:wcproducttable@gmail.com" target="_blank">wcproducttable@gmail.com</a> | <a href="https://wcproducttable.com/tutorials/" target="_blank">Tutorials</a></span>
  </div>
  <?php if( ! defined( 'WCPT_PRO' ) ): ?>
  <div class="wcpt-support wcpt-footer-note">
    <?php wcpt_icon('zap'); ?>
    <span>
      <?php _e( "WCPT PRO is ready for your shop! Build better tables today!", "wc-product-table" ); ?>
      <a href="https://wcproducttable.com/get-pro/" target="_blank"><?php _e( "View enhancements", "wc-product-table" ); ?></a>
    </span>
  </div>
  <?php endif; ?>

  <div class="wcpt-support wcpt-footer-note">
    <?php wcpt_icon('heart'); ?>
    <span><?php _e( "Do you like WCPT? Please support the plugin with your 5 star rating ", "wc-product-table" ); ?><a href="https://wordpress.org/support/plugin/wc-product-table-lite/reviews/" target="_blank">here</a>. Thanks!</span>
  </div>
</div>

<!-- icon templates -->
<?php
  $icons = array( 'trash', 'sliders', 'copy', 'x', 'check' );
  foreach( $icons as $icon_name ){
    ?>
    <script type="text/template" id="wcpt-icon-<?php echo $icon_name; ?>">
      <?php echo wcpt_icon( $icon_name ); ?>
    </script>
    <?php
  }
?>

<!-- element partials -->
<?php require_once('partials/element-editor/element-partials.php'); ?>

<!-- required js vars -->
<?php
  $attributes = wc_get_attribute_taxonomies();
?>
<script>wcpt_attributes = <?php echo json_encode( $attributes ) ?>;</script>
<script>var wcpt_icons_url = "<?php echo WCPT_PLUGIN_URL . '/assets/feather'; ?>";</script>

<!-- embedded style -->
<?php
  $svg_cross_path =  plugin_dir_url( __FILE__ ) . 'assets/css/cross.svg';
?>
<style media="screen">
  .wcpt-block-editor-lightbox-screen {
    cursor: url('<?php echo $svg_cross_path; ?>'), auto;
  }
</style>

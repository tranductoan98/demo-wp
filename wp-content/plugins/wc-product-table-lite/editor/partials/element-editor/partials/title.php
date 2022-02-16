<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="product_link_enabled" />
    Link title to the product's page
  </label>
</div>

<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="product_link_enabled"
  wcpt-condition-val="true"  
>
  <label>
    <input type="checkbox" wcpt-model-key="target_new_page" />
    Open the product link on a new page  
  </label>
</div>

<!-- HTML tag -->
<div class="wcpt-editor-row-option">
  <label>HTML tag <?php wcpt_pro_badge(); ?></label>
  <div class="<?php wcpt_pro_cover(); ?>">
    <select wcpt-model-key="html_tag">
      <?php
        $options = array(
          'span'=> 'span',
          'h1'  => 'H1',
          'h2'  => 'H2',
          'h3'  => 'H3',
          'h4'  => 'H4',
        );
        foreach( $options as $val => $label ){
          echo '<option value="'. $val .'">'. $label .'</option>';
        }
      ?>
    </select>
    <!-- <label>
      <small>
        <?php echo esc_html( "<span> wrapper won't be applied over <a> tag" ); ?>
      </small>
    </label> -->
  </div>
</div>

<!-- style -->
<?php include( 'style/common.php' ); ?>

<!-- condition -->
<?php include( 'condition/outer.php' ); ?>

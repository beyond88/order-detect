
<select class="<?php echo esc_attr( $value['class'] ); ?>" id="<?php echo esc_attr($value['name']); ?>" name="<?php echo esc_html($this->_optionName."[".$value['name']."]"); ?>">
    <?php 
        foreach( $value['default'] as $key => $val ) :
            $selected = '';
            if( isset( $setting_options[ $value['name'] ] ) ) :
                if( $key == $setting_options[ $value['name'] ] ) :
                    $selected ='selected';
                endif;
            endif;             
    ?>
    <option value="<?php echo esc_attr($key); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_attr($val); ?></option>
    <?php 
        endforeach;
    ?>
</select>
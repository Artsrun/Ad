<?php

return array(
    'name' => 'Travel',
    'slug' => 'travel',
    'elements' => array(
        'template' => array(
            'label' => 'Template',
            "required" => "1",
            'childrens' => array( 'heading_one', 'heading_two'),
            'html' => ' <div class="apsa-template-container" style="background-image:url(%apsa_template_bg_img%);background-color:%apsa_template_bg_color%;" >
            <div class="apsa-template-body" style="border-color:%apsa_info_block_color%">
                <div class="apsa-button-wrap" style="background-color:%apsa_link_btn_color%;">
                <a target="%apsa_link_btn_open_type%" style="color:%apsa_link_btn_text_color%;" class="apsa-click-btn" href="%apsa_link_btn_href%">%apsa_link_btn_text%</a>
                </div>
                <div class="apsa-heading-wrap" style="background-color:%apsa_info_block_color%;" >
                    %children% 
                    <div class="apsa-heading-three" style="color:%apsa_footer_heading_color%;"><span>%apsa_footer_heading%</span></div>
                </div>
            </div>
        </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => "Options",
                    'vars' => array(
                        'apsa_template_bg_img' => array('form_type' => 'image', 'value' => '', 'label' => 'Template Background Image'),
                        'apsa_template_bg_color' => array('form_type' => 'color', 'value' => '#ccc', 'label' => 'Template  Background Color'),
                        'apsa_info_block_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Info Block Background Color'),
                        'apsa_link_btn_text' => array('form_type' => 'text', 'value' => 'CLICK HERE', 'label' => 'Link button Text'),
                        'apsa_link_btn_text_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Link button text Color'),
                        'apsa_link_btn_color' => array('form_type' => 'color', 'value' => '#bb3311', 'label' => 'Link button Color'),
                        'apsa_link_btn_href' => array('form_type' => 'text', 'value' => '', 'label' => 'Link button Url', 'class' => 'apsa-link'),
                        'apsa_link_btn_open_type' => array('form_type' => 'select', 'values' => array('New Window' => "_window", "New Tab" => "_blank", 'Self' => "_self"), 'value' => '_window', 'label' => 'Link button open type'),
                        'apsa_footer_heading' => array('form_type' => 'text', 'value' => 'Terms and Conditions May Apply', 'label' => 'Footer Heading Text'),
                        'apsa_footer_heading_color' => array('form_type' => 'color', 'value' => '#bb3311', 'label' => 'Footer Heading Color'),
                    )
                )
            )
        ),
          'heading_one' => array(
            'label' => 'First Heading',
            'html' => '<div class="apsa-heading-one apsa-heading-item" style="color:%apsa_first_heading_color%;"><span>%apsa_first_heading%</span></div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'First Heading',
                    'vars' => array(
                      'apsa_first_heading' => array('form_type' => 'text', 'value' => 'OFF 25%', 'label' => 'First Heading Text'),
                        'apsa_first_heading_color' => array('form_type' => 'color', 'value' => '#bb3311', 'label' => 'First Heading Color'))
                )
            )
        ),
          'heading_two' => array(
            'label' => 'Second Heading',
            'html' => '<div class="apsa-heading-two apsa-heading-item" style="color:%apsa_second_heading_color%;" ><span>%apsa_second_heading%</span></div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Second Heading',
                    'vars' => array(
                    'apsa_second_heading' => array('form_type' => 'text', 'value' => 'ALL NEW ITEMS', 'label' => 'Second Heading Text'),
                    'apsa_second_heading_color' => array('form_type' => 'color', 'value' => '#bb3311', 'label' => 'Second Heading Color'),)
                )
            )
        ),
    ),
);


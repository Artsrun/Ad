<?php

return array(
    'name' => 'Fashion',
    'slug' => 'fashion',
    'elements' => array(
        'template' => array(
            'label' => 'Template',
            "required" => "1",
            'childrens' => array('image_frame', 'info_block'),
            'html' => '   
             <div class="apsa-template-container" style="background-color:%apsa_template_bg_color%;">
               %children%
                
             </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => "Options",
                    'vars' => array(
                        'apsa_template_bg_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Template  Background Color'),
                    )
                )
            )
        ),
        'image_frame' => array(
            'label' => 'Image Frame',
            'html' => '<div class="apsa-block">
                          <div class="apsa-square" style="background-color:%apsa_square_color%;">
                             <div class="apsa-inner-square" style="background-color:%apsa_inner_square_color%;"></div>
                             <div class="apsa-image" style="background-image:url(%apsa_image%);"></div>
                             <div class="apsa-circle-text" style="background-color:%apsa_shape_color%;color:%apsa_shape_text_color%" >
                            <span>%apsa_shape_text%</span>
                        </div>
                    </div>
                </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Image Frame Options',
                    'vars' => array(
                        'apsa_square_color' => array('form_type' => 'color', 'value' => '#cceedd', 'label' => 'Square Background Color'),
                        'apsa_inner_square_color' => array('form_type' => 'color', 'value' => '#ccaa77', 'label' => 'Inner Square Background Color'),
                        'apsa_image' => array('form_type' => 'image', 'value' => '', 'label' => 'Template Image'),
                        'apsa_shape_text' => array('form_type' => 'text', 'value' => 'SALE', 'label' => 'Circle Text'),
                        'apsa_shape_text_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Circle Text Color'),
                        'apsa_shape_color' => array('form_type' => 'color', 'value' => '#ee2244', 'label' => 'Circle Color'))
                )
            )
        ),
        'info_block' => array(
            'label' => 'Info Block',
            'html' => '<div class="apsa-info-block">
                          <div class="apsa-button-wrap" style="background-color:%apsa_button_area%">
                            <a target="%apsa_link_btn_open_type%" style="border-color:%apsa_link_btn_border_color%;color:%apsa_link_btn_text_color%;background-color:%apsa_link_btn_color%;" href="%apsa_link_btn_href%" class="apsa-click-btn">%apsa_link_btn_text%</a>
                          </div>
                          <div class="apsa-link-heading" style="color:%apsa_link_heading_color%">%apsa_link_heading%</div>
                          <div class="apsa-footer-heading" style="color:%apsa_footer_heading_color%">%apsa_footer_heading%</div>
                     </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Info Block Options',
                    'vars' => array(
                        'apsa_button_area' => array('form_type' => 'color', 'value' => '#ccaa77', 'label' => 'Link Button Area Backgound Color'),
                        'apsa_link_btn_text' => array('form_type' => 'text', 'value' => 'SHOP NOW', 'label' => 'Link Button Text'),
                        'apsa_link_btn_text_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Link Button text Color'),
                        'apsa_link_btn_color' => array('form_type' => 'color', 'value' => '#ccaa77', 'label' => 'Link Button Color'),
                        'apsa_link_btn_border_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Link Button Border Color'),
                        'apsa_link_btn_href' => array('form_type' => 'text', 'value' => '', 'label' => 'Link Button Url', 'class' => 'apsa-link'),
                        'apsa_link_btn_open_type' => array('form_type' => 'select', 'values' => array('New Window' => "_window", "New Tab" => "_blank", 'Self' => "_self"), 'value' => '_window', 'label' => 'Link button open type'),
                        'apsa_link_heading' => array('form_type' => 'text', 'value' => 'WWW.YOURSTORE.COM', 'label' => 'Link Heading Text'),
                        'apsa_link_heading_color' => array('form_type' => 'color', 'value' => '#000', 'label' => 'Link Heading Color'),
                        'apsa_footer_heading' => array('form_type' => 'text', 'value' => 'Terms and Conditions May Apply', 'label' => 'Footer Heading Text'),
                        'apsa_footer_heading_color' => array('form_type' => 'color', 'value' => '#000', 'label' => 'Footer Heading Color'))
                )
            )
        ),
    ),
);


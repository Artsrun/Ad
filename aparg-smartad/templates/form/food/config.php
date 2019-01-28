<?php

return array(
    'name' => 'Food',
    'slug' => 'food',
    'elements' => array(
        'template' => array(
            'label' => 'Template',
            "required" => "1",
            'childrens' => array('logo', 'desc_above', 'center_logo', 'desc_below'),
            'html' => '<div class="apsa-template-body" style="background-image:url(%apsa_template_bg_img%);background-color:%apsa_template_bg_color%;">
            <div class="apsa-template-content" style="border-color:%apsa_frame_color%;">
                <div class="apsa-temp-inner-wrap">
                       %children%
                   <div class="apsa-link-btn-wrap">
                        <div class="apsa-link-btn-layer">
                            <a target="%apsa_link_btn_open_type%" style="color:%apsa_link_btn_text_color%;background-color:%apsa_link_btn_color%;" class="apsa-click-btn" href="%apsa_link_btn_href%">%apsa_link_btn_text%</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => "Options",
                    'vars' => array(
                        'apsa_template_bg_img' => array('form_type' => 'image', 'value' => '', 'label' => 'Template Background Image'),
                        'apsa_template_bg_color' => array('form_type' => 'color', 'value' => '#ccc', 'label' => 'Template Background Color'),
                        'apsa_frame_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Frame Color'),
                        'apsa_link_btn_text' => array('form_type' => 'text', 'value' => 'CLICK HERE', 'label' => 'Link Button Text'),
                        'apsa_link_btn_text_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Link Button text Color'),
                        'apsa_link_btn_color' => array('form_type' => 'color', 'value' => '#f4e542', 'label' => 'Link Button Color'),
                        'apsa_link_btn_href' => array('form_type' => 'text', 'value' => '', 'label' => 'Link Button Url', 'class' => 'apsa-link'),
                        'apsa_link_btn_open_type' => array('form_type' => 'select', 'values' => array('New Window' => "_window", "New Tab" => "_blank", 'Self' => "_self"), 'value' => '_window', 'label' => 'Link Button Open Type'),
                    )
                )
            )
        ),
        'logo' => array(
            'label' => 'Logo',
            'html' => ' <div class="apsa-font-logo-wrap">
                        <div>
                            <span class="apsa-font-logo" style="background-image:url(%apsa_logo_img%);"></span>
                        </div>
                    </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Logo',
                    'vars' => array('apsa_logo_img' => array('form_type' => 'image', 'value' => '', 'label' => 'Image Path'))
                )
            )
        ),
        'desc_above' => array(
            'label' => 'First Heading',
            'html' => ' <div class="apsa-above-title" >
                            <div style="color:%apsa_description_above_color%;"> %apsa_description_above%</div>
                        </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'First Heading',
                    'vars' => array(
                        'apsa_description_above' => array('form_type' => 'text', 'value' => 'SPECIAL OFFER', 'label' => 'First Heading Text'),
                        'apsa_description_above_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'First Heading  Color')
                    )
                )
            )
        ),
        'center_logo' => array(
            'label' => 'Center Heading',
            'html' => '<div class="apsa-center-logo-wrap" >
                         <div class="apsa-center-logo"> 
                            <span>
                            <span>
                            <span style="color:%apsa_center_logo_color%;" class="apsa-center-logo-text">%apsa_center_logo%</span>
                                <div style="background-color:%apsa_shape_color%;color:%apsa_shape_text_color%"  class="apsa-center-shape">
                                <span>%apsa_shape_text%</span>
                               </div>
                            </span>
                            </span>
                        </div>
                    </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Center Heading',
                    'vars' => array(
                        'apsa_center_logo' => array('form_type' => 'text', 'value' => 'Free Sushi', 'label' => 'Center Heading Text'),
                        'apsa_center_logo_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Center Heading Color'),
                        'apsa_shape_text' => array('form_type' => 'text', 'value' => 'PICK YOURS', 'label' => 'Circle Text'),
                        'apsa_shape_text_color' => array('form_type' => 'color', 'value' => '#000', 'label' => 'Circle Text Color'),
                        'apsa_shape_color' => array('form_type' => 'color', 'value' => '#f4e542', 'label' => 'Circle Color')
                    )
                )
            )
        ),
        'desc_below' => array(
            'label' => 'Third Heading',
            'html' => ' <div class="apsa-below-title" >
                           <div style="color:%apsa_description_below_color%;"> %apsa_description_below%</div>
                        </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Third Heading',
                    'vars' => array(
                        'apsa_description_below' => array('form_type' => 'text', 'value' => 'ON WEEKEND', 'label' => 'Third Heading Text'),
                        'apsa_description_below_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Third Heading Color')
                    )
                )
            )
        ),
    ),
);


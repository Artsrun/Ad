<?php

return array(
    'name' => 'Decor',
    'slug' => 'decor',
    'elements' => array(
        'info_block' => array(
            'label' => 'Info Block',
            "required" => "1",
            'childrens' => array('logo', 'click_button', 'heading_one', 'heading_two', 'heading_three'),
            'html' => '
             <div class="apsa-info-block-wrap">
                    <div class="apsa-first-layer" style="background-color:%apsa_info_bg_color%" ></div>
			<div class="apsa-second-layer" style="background-color:%apsa_info_bg_color%"></div>
			<div class="apsa-info-block">
				%children%
                        </div>
                    </div> ',
            'var_groups' => array(
                'options' => array(
                    'label' => "Options",
                    'vars' => array(
                        'apsa_info_bg_color' => array('form_type' => 'color', 'value' => '#66aa99', 'label' => 'Info Block Background Color'),
                    )
                )
            )
        ),
        'heading_one' => array(
            'label' => 'First Heading',
            'html' => '<p class="apsa-heading-one apsa-heading" style="color:%apsa_h1_color%;"><span>%apsa_heading_one%</span></p>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'First Heading',
                    'vars' => array(
                        'apsa_heading_one' => array('form_type' => 'text', 'value' => 'INTERIOR', 'label' => 'First Heading Text'),
                        'apsa_h1_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'First Headings Color'))
                )
            )
        ),
        'heading_three' => array(
            'label' => 'Third Heading',
            'html' => '<p class="apsa-heading-three apsa-heading" style="color:%apsa_h3_color%;"><span>%apsa_heading_three%</span></p>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Third Heading',
                    'vars' => array(
                        'apsa_heading_three' => array('form_type' => 'text', 'value' => 'FREE CONSULTATION FOR TODAY', 'label' => 'Third Heading Text'),
                        'apsa_h3_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Third Headings Color')
                    )
                )
            )
        ),
        'heading_two' => array(
            'label' => 'Second Heading',
            'html' => '<p class="apsa-heading-two apsa-heading" style="color:%apsa_h2_color%;"><span>%apsa_heading_two%</span></p>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Second Heading',
                    'vars' => array(
                        'apsa_heading_two' => array('form_type' => 'text', 'value' => 'DECOR', 'label' => 'Second Heading Text'),
                        'apsa_h2_color' => array('form_type' => 'color', 'value' => '#ccee00', 'label' => 'Second Headings Color'),
                    )
                )
            )
        ),
        'logo' => array(
            'label' => 'Logo',
            'html' => '<div class="apsa-logo-title apsa-movable-element">
                                   <div class="apsa-font-logo" style="background-image:url(%apsa_logo_img%);"></div>
                        </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Logo',
                    'vars' => array('apsa_logo_img' => array('form_type' => 'image', 'value' => '', 'label' => 'Image Path'))
                )
            )
        ),
        'click_button' => array(
            'label' => 'Button',
            'html' => ' <div class="apsa-button-wrap apsa-movable-element">
                                 <a target="%apsa_link_btn_open_type%" style="color:%apsa_link_btn_text_color%;background-color:%apsa_link_btn_color%;"  class="apsa-click-btn" href="%apsa_link_btn_href%">%apsa_link_btn_text%</a>
                       </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Button',
                    'vars' => array(
                        'apsa_link_btn_text' => array('form_type' => 'text', 'value' => 'CLICK HERE', 'label' => 'Link button Text'),
                        'apsa_link_btn_text_color' => array('form_type' => 'color', 'value' => '#224444', 'label' => 'Link button text Color'),
                        'apsa_link_btn_color' => array('form_type' => 'color', 'value' => '#EEF0E5', 'label' => 'Link button Color'),
                        'apsa_link_btn_href' => array('form_type' => 'text', 'value' => '', 'label' => 'Link button Url', 'class' => 'apsa-link'),
                        'apsa_link_btn_open_type' => array('form_type' => 'select', 'values' => array('New Window' => "_window", "New Tab" => "_blank", 'Self' => "_self"), 'value' => '_window', 'label' => 'Link button open type'),
                    )
                )
            )
        ),
        'template_img' => array(
            "label" => 'Template Image',
            'html' => '<div  class="apsa-template-image" style="background-image:url(%apsa_template_bg_img%);background-color:%apsa_template_bg_color%"></div>',
            'var_groups' => array(
                'options' => array(
                    'label' => "Options",
                    'vars' => array(
                        'apsa_template_bg_img' => array('form_type' => 'image', 'value' => '', 'label' => 'Background Image'),
                        'apsa_template_bg_color' => array('form_type' => 'color', 'value' => '#ccc', 'label' => 'Background Color'),
                    )
                )
            ),
        ),
    ),
);


<?php

return array(
    'name' => 'Sport',
    'slug' => 'sport',
    'elements' => array(
        'info_block' => array(
            'label' => 'Info Block',
            "required" => "1",
            'childrens' => array('logo', 'center_headings', 'click_button'),
            'html' => '
            <div class="apsa-info-block" style="background-color:%apsa_info_bg_color%">
              %children%
               
            </div> ',
            'var_groups' => array(
                'options' => array(
                    'label' => "Options",
                    'vars' => array(
                        'apsa_info_bg_color' => array('form_type' => 'color', 'value' => '#224444', 'label' => 'Info Column Background Color'),
                    )
                )
            )
        ),
        'click_button' => array(
            'label' => 'Button',
            'html' => '<div class="apsa-button-wrap apsa-movable-element">
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
        'center_headings' => array(
            'label' => 'Center Heading',
            'html' => ' 
                <div class="apsa-heading-container">
                    <p class="apsa-heading-one" style="color:%apsa_center_heading_color%;"><span> %apsa_heading_one%</span></p>
                    <p class="apsa-heading-two" style="color:%apsa_center_heading_color%;"><span>  %apsa_heading_two%</span></p>
                    <p class="apsa-heading-three" style="color:%apsa_center_heading_color%;"><span>  %apsa_heading_three%</span></p>
                    <hr class="apsa-horizontal-ruler"/>
                    <p class="apsa-headiing-time" style="color:%apsa_time_color%;"><span> %apsa_time_text%</span></p>
                </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Center Headings',
                    'vars' => array(
                        'apsa_heading_one' => array('form_type' => 'text', 'value' => 'SOFT', 'label' => 'First Heading'),
                        'apsa_heading_two' => array('form_type' => 'text', 'value' => 'OPENING', 'label' => 'Second Heading'),
                        'apsa_heading_three' => array('form_type' => 'text', 'value' => 'TODAY', 'label' => 'Third Heading'),
                        'apsa_center_heading_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Center Headings Color'),
                        'apsa_time_text' => array('form_type' => 'text', 'value' => '09 AM - 10 PM', 'label' => 'Time Section Text'),
                        'apsa_time_color' => array('form_type' => 'color', 'value' => '#fff', 'label' => 'Time Section Color'),
                    )
                )
            )
        ),
        'logo' => array(
            'label' => 'Logo',
            'html' => '<div class="apsa-logo-title apsa-movable-element" >
                         <span class="apsa-font-logo" style="background-image:url(%apsa_logo_img%);"></span>                
                       </div>',
            'var_groups' => array(
                'options' => array(
                    'label' => 'Logo',
                    'vars' => array('apsa_logo_img' => array('form_type' => 'image', 'value' => '', 'label' => 'Image Path'))
                )
            )
        ),
        'template_img' => array(
            "label" => 'Template Image',
            'html' => '<div  class="apsa-template-image" style="background-position: center;background-image:url(%apsa_template_bg_img%);background-color:%apsa_template_bg_color%"></div>',
            'var_groups' => array(
                'options' => array(
                    'label' => "Options",
                    'vars' => array(
                        'apsa_template_bg_img' => array('form_type' => 'image', 'value' => '', 'label' => 'Image Path'),
                        'apsa_template_bg_color' => array('form_type' => 'color', 'value' => '#ccc', 'label' => 'Background Color'),
                    )
                )
            ),
        ),
    ),
);


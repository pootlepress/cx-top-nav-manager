<?php
/**
 * Created by Alan on 31/10/2014.
 */

if (!class_exists('PootlePress_Canvas_Options')) {
    class PootlePress_Canvas_Options {

        private $addedOptionData;

        public function __construct() {
            $this->addedOptionData = array();

            // this filter will and should be run before the theme call the get_option('woo_template')
            add_filter('option_woo_template', array($this, 'filter_get_option'));

            add_action('admin_head', array($this, 'admin_css'));
        }

        public function admin_css() {
            echo "<style>\n" .
                '.settings-section p.description.custom { display: inline-block; }' . "\n" .
                "</style>\n";
        }

        // user of this class should call add_option at initialization, before any hook is executed
        // also make sure doesn't add options that was added before
        public function add_options($afterName, $afterType, $options) {

            // add this option
            $this->addedOptionData[] = array(
                'afterName' => $afterName,
                'afterType' => $afterType,
                'options' => $options
            );

            return true;
        }

        private function find_index_of_option($allOptions, $optionData) {
            $index = 0;
            $foundIndex = -1;
            foreach ($allOptions as $option) {
                if (isset($option['name']) && isset($option['type']) &&
                    isset($optionData['afterName']) && isset($optionData['afterType']))
                {
                    if ($option['name'] == $optionData['afterName'] &&
                        $option['type'] == $optionData['afterType']
                    ) {
                        $foundIndex = $index;
                        return $foundIndex;
                    }
                }

                ++$index;
            }

            return $foundIndex;
        }

        public function filter_get_option($allOptions) {

            foreach ($this->addedOptionData as $optionData) {
                if (!isset($optionData['options'])) {
                    continue;
                }

                $index = $this->find_index_of_option($allOptions, $optionData);
                if ($index >= 0) {

                    // check if it is already added
                    $temp = array_slice($allOptions, $index + 1, count($optionData['options']));
                    if ($temp != $optionData['options']) {
                        // insert after the index
                        array_splice($allOptions, $index + 1, 0, $optionData['options']);

                    }

                } else {

                    // insert at the end
                    array_splice($allOptions, count($allOptions), 0, $optionData['options']);
                }
            }

            return $allOptions;
        }
    }
}

if (!isset($GLOBALS['PCO'])) {
    $GLOBALS['PCO'] = new PootlePress_Canvas_Options();
}
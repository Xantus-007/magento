<?php

class Monbento_Site_Block_Rewrite_Core_Messages extends Mage_Core_Block_Messages
{
    
    public function getGroupedHtmlMonbento()
    {
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );
        $html = '';
        foreach ($types as $type) {
            switch($type)
            {
                case Mage_Core_Model_Message::ERROR:
                    $class = 'alert-box alert';
                    break;
                case Mage_Core_Model_Message::WARNING:
                    $class = 'alert-box warning';
                    break;
                case Mage_Core_Model_Message::NOTICE:
                    $class = 'alert-box secondary';
                    break;
                case Mage_Core_Model_Message::SUCCESS:
                    $class = 'alert-box success';
                    break;
            }
            if ( $messages = $this->getMessages($type) ) {
                if( !$html ) {
                    $html = '<div class="row">';
                }
                $html .= '<div class="columns"><div data-alert class="'.$class.'"><div class="o-table"><div class="o-table__cell--valignMiddle"><div class="c-wysiwyg"><ul>';
                foreach ( $messages as $message ) {
                    $html.= '<li>';
                    $html.= ($this->_escapeMessageFlag) ? $this->escapeHtml($message->getText()) : $message->getText();
                    $html.= '</li>';
                }
                $html.= '</ul></div></div></div><a href="#" class="close"><i class="c-fonticon__icon--delete"></i></a></div></div>';
            }
        }
        if ( $html) {
            $html .= '</div>';
        }
        return $html;
    }

}

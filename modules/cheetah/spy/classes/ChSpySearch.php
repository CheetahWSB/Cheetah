<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once( CH_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $GLOBALS['tmpl'] . '/scripts/ChTemplSearchResultText.php');
    require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbPaginate.php' );
    require_once( 'ChSpyModule.php' );

    class ChSpySearch extends ChTemplSearchResultText
    {
        var $oSpyObject;
        var $aModule;

        /**
         * Class constructor ;
         */
        function __construct($oSpyObject = null)
        {
            // call the parent constructor ;
            parent::__construct();

            if(!$oSpyObject) {
                $this -> oSpyObject = ChWsbModule::getInstance('ChSpyModule');
            } else {
                $this -> oSpyObject = $oSpyObject;
            }

            // init some needed db table's fields ;

            /* main settings for shared modules
               ownFields - fields which will be got from main table ($this->aCurrent['table'])
               searchFields - fields which using for full text key search
               join - array of join tables
                    join array (
                        'type' - type of join
                        'table' - join table
                        'mainField' - field from main table for 'on' condition
                        'onField' - field from joining table for 'on' condition
                        'joinFields' - array of fields from joining table
                    )
            */

            $this -> aCurrent = array (

                // module name ;
                'name'  => 'spy',
                'title' => '_ch_spy',
                'table' => $this -> oSpyObject -> _oDb -> sTablePrefix . 'data',

                'ownFields'     => array('id', 'sender_id', 'lang_key', 'params', 'date'),

                'join' => array(
                    'profile' => array(
                        'type' => 'left',
                        'table' => 'Profiles',
                        'mainField' => 'sender_id',
                        'onField' => 'ID',
                        'joinFields' => array('NickName'),
                    ),
                ),

                'restriction' => array (
                    'global'   => array('value'=>'', 'field'=>'', 'operator'=>'='),
                    'friends'  => array('value' => '', 'field' => 'friend_id', 'operator'=>'=', 'table' => $this -> oSpyObject -> _oDb -> sTablePrefix . 'friends_data'),
                    'no_my'    => array('value'=>'', 'field'=>'sender_id', 'operator'=>'<>', 'table' => $this -> oSpyObject -> _oDb -> sTablePrefix . 'data'),
                    'over_id'  => array('value'=>'', 'field'=>'id', 'operator'=>'>', 'table' => $this -> oSpyObject -> _oDb -> sTablePrefix . 'data'),
                    'type'     => array('value'=>'', 'field'=>'type', 'operator'=>'=', 'table' => $this -> oSpyObject -> _oDb -> sTablePrefix . 'data'),
                    'only_me'  => array('value'=>'', 'field'=>'recipient_id', 'operator'=>'=', 'table' => $this -> oSpyObject -> _oDb -> sTablePrefix . 'data'),
                    'viewed'   => array('value'=>'', 'field'=>'viewed', 'operator'=>'in', 'table' => $this -> oSpyObject -> _oDb -> sTablePrefix . 'data'),
                ),

                'paginate' => array( 'perPage' => $this -> oSpyObject -> _oConfig -> iPerPage, 'page' => 1, 'totalNum' => 10, 'totalPages' => 1),
                'sorting' => 'last',
                'view' => 'full',
                'ident' => 'id'
            );
        }

        /**
         * Function will generate page's pagination;
         *
         * @param  : $aParams (array) - an array with params (path to current module, 'on change page' script, etc);
         * @return : (text) - html presentation data;
         */
        function showPagination($aParams = array())
        {
            $sModulePath = '';
            if(isset($aParams['module_path']) && !empty($aParams['module_path']))
                $sModulePath = $aParams['module_path'];
            else
                $sModulePath = ch_append_url_params($this->oSpyObject->_oConfig->getBaseUri(), array('type' => 'all'));

            $sScript = isset($aParams['on_change_page']) && !empty($aParams['on_change_page']) ? $aParams['on_change_page'] : null;

            $aPaginate = array(
                'count' => $this -> aCurrent['paginate']['totalNum'],
                'per_page' => $this -> aCurrent['paginate']['perPage'],
                'page' => $this -> aCurrent['paginate']['page'],
                'page_url' => $sModulePath . '&page={page}&per_page={per_page}',
                'on_change_page' => $sScript,
                'on_change_per_page' => null
            );

            $oPaginate = new ChWsbPaginate($aPaginate);
            return '<div class="clear_both"></div>' . $oPaginate -> getSimplePaginate(null, -1, -1, false);
        }

        function getAlterOrder ()
        {/*
            return array(
                'groupBy' => " GROUP BY `{$this -> oSpyObject -> _oDb -> sTablePrefix}events`.`id`",
            );*/
        }

        function getLimit ()
        {
            if( isset($this->aCurrent['paginate']['unlimit']) ) {
                return;
            } else if( isset($this->aCurrent['paginate']['limit']) ){
                return 'LIMIT ' . $this->aCurrent['paginate']['limit'];
            } else if (isset($this->aCurrent['paginate'])) {
                $sqlFrom = ( $this->aCurrent['paginate']['page'] - 1 ) * $this->aCurrent['paginate']['perPage'];
                $sqlTo = $this->aCurrent['paginate']['perPage'];
                return 'LIMIT ' . $sqlFrom .', '.$sqlTo;
            }
        }
    }

<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbModuleDb.php');

class ChSimpleMessengerDb extends ChWsbModuleDb
{
    var $_oConfig;

    var $sTablePrefix;

    /**
     * Constructor.
     */
    function __construct(&$oConfig)
    {
        parent::__construct();

        $this->_oConfig     = $oConfig;
        $this->sTablePrefix = $oConfig->getDbPrefix();
    }

    /**
     * Function will create new message ;
     *
     * @param   : $iSenderId (integer)    - sender Id;
     * @param   : $iRecipientId (integer) - recipient Id;
     * @param   : $sMessage (string)      - message text;
     * @return  : (integer) - number of affected rows ;
     */
    function createMessage($iSenderId, $iRecipientId, $sMessage)
    {
        // procces vars
        $iSenderId    = (int)$iSenderId;
        $iRecipientId = (int)$iRecipientId;
        $sMessage     = process_db_input($sMessage, CH_TAGS_NO_ACTION);

        $sQuery =
            "
                INSERT INTO
                    `{$this -> sTablePrefix}messages`
                SET
                    `SenderID`      = {$iSenderId},
                    `RecipientID`   = {$iRecipientId},
                    `Message`       = '{$sMessage}'
            ";

        return (int)$this->query($sQuery) > 0 ? $this->lastId() : false;
    }

    /**
     * Function will close chat window;
     *
     * @param   : $iLoggedMember (integer) - current's logged member;
     * @param   : $iRecipientId (integer) - recepient's Id;
     * @return  : (integer) - number of affected rows ;
     */
    function closeChatWindow($iRecipientId, $iLoggedMember)
    {
        $iRecipientId  = (int)$iRecipientId;
        $iLoggedMember = (int)$iLoggedMember;

        // define the sender's id;
        $sQuery =
            "
                SELECT
                    `SenderID`
                FROM
                    `{$this -> sTablePrefix}messages`
                WHERE
                    (
                        `SenderID` = {$iLoggedMember}
                            AND
                        `RecipientID` = {$iRecipientId}
                    )
                        OR
                    (
                        `SenderID` = {$iRecipientId}
                            AND
                        `RecipientID` = {$iLoggedMember}
                    )
                ORDER BY
                    `Date` DESC
                LIMIT 1
            ";

        $iSenderId = $this->getOne($sQuery);
        $sFieldId  = ($iSenderId == $iLoggedMember) ? 'SenderStatus' : 'RecipientStatus';

        $sQuery =
            "
                UPDATE
                    `{$this -> sTablePrefix}messages`
                SET
                    `{$sFieldId}` = 'close'
                WHERE
                    (
                        `SenderID` = {$iLoggedMember}
                            AND
                        `RecipientID` = {$iRecipientId}
                    )
                        OR
                    (
                        `SenderID` = {$iRecipientId}
                            AND
                        `RecipientID` = {$iLoggedMember}
                    )
                ORDER BY
                    `Date` DESC
                LIMIT 1
            ";

        return $this->query($sQuery);
    }

    /**
     * Function will delete profile's history;
     *
     * @param  : $iProfileId (integer) - profile's Id;
     * @return : void;
     */
    function deleteAllMessagesHistory($iProfileId)
    {
        $iProfileId = (int)$iProfileId;

        $sQuery =
            "
                DELETE FROM
                    `{$this -> sTablePrefix}messages`
                WHERE
                    `SenderID` = {$iProfileId}
                        OR
                    `RecipientID` = {$iProfileId}
            ";

        $this->query($sQuery);
    }

    /**
     * Function will delete messages history ;
     *
     * @param  : $iSender (integer)         - sender member's Id;
     * @param  : $iRecipient (integer)      - recipient member's Id;
     * @param  : $iAllowCountMessages integer;
     *
     */
    function deleteMessagesHistory($iSender, $iRecipient, $iAllowCountMessages)
    {
        $iSender             = (int)$iSender;
        $iRecipient          = (int)$iRecipient;
        $iAllowCountMessages = (int)$iAllowCountMessages;

        $sQuery =
            "
                SELECT
                    COUNT(*)
                FROM
                    `{$this -> sTablePrefix}messages`
                WHERE
                    (
                        `SenderID` = {$iSender}
                            AND
                        `RecipientID` = {$iRecipient}
                    )
                        OR
                    (
                        `SenderID` = {$iRecipient}
                            AND
                        `RecipientID` = {$iSender}
                    )
            ";

        $iMessageCount = (int)$this->getOne($sQuery);
        if ($iMessageCount > $iAllowCountMessages) {
            // delete all unnecessary messages ;
            $iRowsDelete = $iMessageCount - $iAllowCountMessages;

            $sQuery =
                "
                    DELETE FROM
                        `{$this -> sTablePrefix}messages`
                    WHERE
                        (
                            `SenderID` = {$iSender}
                                AND
                            `RecipientID` = {$iRecipient}
                        )
                            OR
                        (
                            `SenderID` = {$iRecipient}
                                AND
                            `RecipientID` = {$iSender}
                        )
                    ORDER BY `ID`
                    LIMIT {$iRowsDelete}
                ";

            $this->query($sQuery);
        }
    }

    /**
     * Function will get the last message's id for current chat box;
     *
     * @param  : $iSender (integer)         - sender member's Id;
     * @param  : $iRecipient (integer)      - recipient member's Id;
     * @return : (integer) - the last message's id;
     */
    function getLastMessagesId($iRecipient, $iSender)
    {
        $iRecipient = (int)$iRecipient;
        $iSender    = (int)$iSender;

        $sQuery =
            "
                SELECT
                    `ID`
                FROM
                    `{$this -> sTablePrefix}messages`
                WHERE
                    (
                        `SenderID` = {$iSender}
                            AND
                        `RecipientID` = {$iRecipient}
                            OR
                        `SenderID` = {$iRecipient}
                            AND
                        `RecipientID` = {$iSender}
                    )
                ORDER BY
                    `ID` DESC
                LIMIT 1
            ";

        return $this->getOne($sQuery);
    }

    /**
     * Function will get count of user's active chat boxes;
     *
     * @param  : $iSender (integer) - sender's id;
     * @return : (array)  - return array with all sender's chat boxes (recipients id);
     * [RecipientID] - (string)  recipient's Id;
     */
    function getChatBoxesCount($iSender)
    {
        $iSender = (int)$iSender;

        $sQuery =
            "
                SELECT
                    DISTINCT IF(`{$this -> sTablePrefix}messages`.`SenderID` = {$iSender}, `{$this -> sTablePrefix}messages`.`RecipientID`, `{$this -> sTablePrefix}messages`.`SenderID`) AS `RecipientID`
                FROM
                    `{$this -> sTablePrefix}messages`
                INNER JOIN
                    `Profiles`
                ON
                    `Profiles`.`ID` = {$iSender}
                WHERE
                    `{$this -> sTablePrefix}messages`.`RecipientID` = {$iSender}
                        OR
                    `{$this -> sTablePrefix}messages`.`SenderID` = {$iSender}
            ";

        $aSenders          = $this->getAll($sQuery);
        $aProcessedSenders = array();

        // procces all recived id;
        foreach ($aSenders as $iKey => $aItems) {
            $aItems['RecipientID'] = (int)$aItems['RecipientID'];

            $sQuery =
                "
                    SELECT
                        IF(`SenderID` = {$aItems['RecipientID']}, `SenderStatus`, `RecipientStatus`) AS `Status`
                    FROM
                        `{$this -> sTablePrefix}messages`
                    WHERE
                        (
                            `RecipientID` = {$aItems['RecipientID']}
                                AND
                            `SenderID` = {$iSender}
                        )
                            OR
                        (
                            `RecipientID` = {$iSender}
                                AND
                            `SenderID` = {$aItems['RecipientID']}
                        )
                        ORDER BY
                            `Date` DESC
                        LIMIT 1
                ";

            if ($this->getOne($sQuery) != 'close') {
                $aProcessedSenders[] = $aItems['RecipientID'];
            }
        }

        return $aProcessedSenders;
    }

    /**
     * Function will get the chat box's number of messages;
     *
     * @param  : $iSender (integer)         - sender member's Id;
     * @param  : $iRecipient (integer)      - recipient member's Id;
     * @return : (integer) - number of messages;
     */
    function getMessagesCount($iRecipient, $iSender)
    {
        $iRecipient = (int)$iRecipient;
        $iSender    = (int)$iSender;

        $sQuery =
            "
                SELECT
                    COUNT(*)
                FROM
                    `{$this -> sTablePrefix}messages`
                WHERE
                    (
                        `SenderID` = {$iSender}
                            AND
                        `RecipientID` = {$iRecipient}
                    )
                        OR
                    (
                        `SenderID` = {$iRecipient}
                            AND
                        `RecipientID` = {$iSender}
                    )
            ";

        return $this->getOne($sQuery);
    }

    /**
     * Function will generate member's messages history ;
     *
     * @param  : $aCoreSettings (array)     - chat's core settings;
     * @param  : $iSender (integer)         - sender member's Id;
     * @param  : $iRecipient (integer)      - recipient member's Id;
     * @param  : $iLastMessageId (integer)  - last message's Id (query will return all rows after this value);
     * @param  : $iMessageLimit (integer)   - rows limit ;
     * @return : array;
     * [ ID ]          - (integer) message's Id ;
     * [ Message ]     - (string)  message string ;
     * [ SenderID ]    - (integer) message's sender Id ;
     * [ RecipientID ] - (integer) message's recipient Id ;
     * [ Date ]        - (string)  when message was created ;
     */
    function getHistoryList(&$aCoreSettings, $iRecipient, $iSender, $iLastMessageId = 0, $iMessageLimit = 0)
    {
        $iRecipient     = (int)$iRecipient;
        $iSender        = (int)$iSender;
        $iLastMessageId = (int)$iLastMessageId;
        $iMessageLimit  = (int)$iMessageLimit;

        // define the rows limit ;
        $sRowsLimit = ($iMessageLimit) ? " LIMIT {$iMessageLimit}" : null;

        // check if chat history is enabled now;
        if ($aCoreSettings['save_chat_history'] && !$sRowsLimit) {

            $iMessagesCount = $this->getMessagesCount($iRecipient, $iSender);
            $iLimitFrom     = $iMessagesCount - $aCoreSettings['number_visible_messages'];
            $sRowsLimit     = " LIMIT {$iLimitFrom}, 18446744073709551615";
        }

        $sQuery =
            "
                SELECT
                   `ID`, `Message`, `SenderID`,
                   `RecipientID`, UNIX_TIMESTAMP(`Date`) AS `DateTS`
                FROM
                    `{$this -> sTablePrefix}messages`
                WHERE
                    (
                        (
                           `SenderID` = {$iSender}
                                AND
                            `RecipientID` = {$iRecipient}
                        )
                                OR
                        (
                            `SenderID` = {$iRecipient}
                                AND
                            `RecipientID` = {$iSender}
                        )
                    )
                        AND
                    (
                        `ID` > {$iLastMessageId}
                    )
                ORDER BY
                    `ID`
                {$sRowsLimit}
            ";

        return $this->getAll($sQuery);
    }

    /**
     * Function will generate list of members;
     *
     * @param  : $iRecipientId (integer) - recipient Id ;
     * @param  : $aRegBoxes (array) - registered messages box;
     * @return : (array) - with members id ;
     * [SenderID] (integer) - sender's id;
     */
    function getNewChatBoxes($iRecipientId, $aRegBoxes = array())
    {
        $iRecipientId = (int)$iRecipientId;

        // define registered chat boxes;
        $sFilter = '';
        if ($aRegBoxes && is_array($aRegBoxes)) {
            foreach ($aRegBoxes as $iKey => $aItem) {
                $iKey = (int)$iKey;
                $sFilter .= " AND (`{$this -> sTablePrefix}messages`.`SenderID` <> {$iKey}  AND  `{$this -> sTablePrefix}messages`.`RecipientID` <> {$iKey})";
            }
        }

        $sQuery =
            "
                SELECT
                    DISTINCT IF(`{$this -> sTablePrefix}messages`.`SenderID` = {$iRecipientId},  `{$this -> sTablePrefix}messages`.`RecipientID`,  `{$this -> sTablePrefix}messages`.`SenderID`) AS `RecipientID`
                FROM
                    `{$this -> sTablePrefix}messages`
                INNER JOIN
                    `Profiles`
                ON
                    `Profiles`.`ID` = `RecipientID`
                WHERE
                (
                    `{$this -> sTablePrefix}messages`.`RecipientID` = {$iRecipientId}
                        OR
                    `{$this -> sTablePrefix}messages`.`SenderID` = {$iRecipientId}
                )
                    {$sFilter}
            ";

        $aSenders          = $this->getAll($sQuery);
        $aProcessedSenders = array();

        // procces all recived id;
        foreach ($aSenders as $iKey => $aItems) {
            $aItems['RecipientID'] = (int)$aItems['RecipientID'];

            $sQuery =
                "
                    SELECT
                        IF(`SenderID` = {$aItems['RecipientID']}, `SenderStatus`, `RecipientStatus`) AS `Status`
                    FROM
                        `{$this -> sTablePrefix}messages`
                    WHERE
                        (
                            `RecipientID` = {$aItems['RecipientID']}
                                AND
                            `SenderID` = {$iRecipientId}
                        )
                            OR
                        (
                            `RecipientID` = {$iRecipientId}
                                AND
                            `SenderID` = {$aItems['RecipientID']}
                        )
                        ORDER BY
                            `Date` DESC
                        LIMIT 1
                ";

            if ($this->getOne($sQuery) != 'close') {
                $aProcessedSenders[] = $aItems['RecipientID'];
            }
        }

        return $aProcessedSenders;
    }

    /**
     * Function will create member's privacy group;
     *
     * @param : $iMemberId (integer)    - member's Id;
     * @param : $iGroupValue (integer)  - privacy group's value;
     */
    function createPrivacyGroup($iMemberId, $iGroupValue = 0)
    {
        $iMemberId   = (int)$iMemberId;
        $iGroupValue = (int)$iGroupValue;

        $sQuery = "SELECT COUNT(*) FROM `{$this -> sTablePrefix}privacy` WHERE `author_id` = {$iMemberId}";
        if ($this->getOne($sQuery)) {
            // update existeng';
            $sQuery = "UPDATE `{$this -> sTablePrefix}privacy` SET `allow_contact_to` = {$iGroupValue} WHERE `author_id` = {$iMemberId}";
            $this->query($sQuery);
        } else {
            // create new;
            $sQuery = "INSERT INTO `{$this -> sTablePrefix}privacy` SET `allow_contact_to` = {$iGroupValue}, `author_id` = {$iMemberId}";
            $this->query($sQuery);
        }
    }

    /**
     * Function will get privacy group value for member's Id;
     *
     * @param  : $iMemberId (integer)    - member's Id;
     * @return : (integer);
     */
    function getPrivacyGroupValue($iMemberId)
    {
        $iMemberId = (int)$iMemberId;

        $sQuery = "SELECT `allow_contact_to` FROM `{$this -> sTablePrefix}privacy` WHERE `author_id` = {$iMemberId}";

        return $this->getOne($sQuery);
    }

    /**
     * Function will protect received data with backlashes ;
     *
     * @param  : $sData (string) - text data ;
     * @return : (string) - protected data ;
     */
    function shieldData($sData)
    {
        return process_db_input($sData, CH_TAGS_NO_ACTION);
    }
}

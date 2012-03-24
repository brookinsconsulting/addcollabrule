<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish addcollabrule extension
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 2007 Kristof Coomans <http://blog.kristofcoomans.be>
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

include_once( 'kernel/classes/ezworkflowtype.php' );
include_once( 'kernel/classes/ezcontentobject.php' );

class AddCollabRuleType extends eZWorkflowEventType
{
    function AddCollabRuleType()
    {
        $this->eZWorkflowEventType( 'addcollabrule', ezi18n( 'extension/projects', 'Add collaboration rule' ) );
        // limit workflows which use this event to be used only on the post-publish trigger
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function typeFunctionalAttributes()
    {
        return array( 'handler', 'selection' );
    }

    function &attributeDecoder( &$event, $attr )
    {
        $retValue = null;
        switch( $attr )
        {
            case 'handler':
            {
                include_once( 'kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationhandler.php' );
                $retValue = new eZCollaborationNotificationHandler();
            } break;

            case 'selection':
            {
                $retValue = array();
                if ( trim( $event->attribute( 'data_text1' ) ) != '' )
                {
                    $retValue = explode( ',', $event->attribute( 'data_text1' ) );
                }
            } break;

            default:
            {
                eZDebug::writeNotice( 'unknown attribute:' . $attr, 'AddCollabRuleType' );
            }
        }
        return $retValue;
    }

    function fetchHTTPInput( &$http, $base, &$event )
    {
        $selectionVar = 'CollaborationHandlerSelection_' . $event->attribute( 'id' );
        if ( $http->hasPostVariable( $selectionVar ) )
        {
            $selection = $http->postVariable( $selectionVar );
            $event->setAttribute( 'data_text1', implode( ',', $selection ) );
        }
    }

    function execute( &$process, &$event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $object =& eZContentObject::fetch( $parameters['object_id'] );

        $collaborationIdentifierList = $event->attribute( 'selection' );

        foreach ( $collaborationIdentifierList as $collaborationIdentifier )
        {
            include_once('kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php');
            $existing = &eZCollaborationNotificationRule::fetchItemTypeList( $collaborationIdentifier, array( $object->attribute( 'id' ) ) );

            if ( count( $existing ) == 0 )
            {
                $rule = &eZCollaborationNotificationRule::create( $collaborationIdentifier, $object->attribute( 'id' ) );
                $rule->store( );
            }
        }

        return EZ_WORKFLOW_TYPE_STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerType( 'addcollabrule', 'AddCollabRuleType' );

?>
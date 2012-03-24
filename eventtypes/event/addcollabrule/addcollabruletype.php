<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish addcollabrule extension
// SOFTWARE RELEASE: 2.x
// COPYRIGHT NOTICE: Copyright (C) 2007-2008 Kristof Coomans <http://blog.kristofcoomans.be>
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

class AddCollabRuleType extends eZWorkflowEventType
{
    function __construct()
    {
        $this->eZWorkflowEventType( 'addcollabrule', ezpI18n::tr( 'extension/projects', 'Add collaboration rule' ) );
        // limit workflows which use this event to be used only on the post-publish trigger
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function typeFunctionalAttributes()
    {
        return array( 'handler', 'selection' );
    }

    function attributeDecoder( $event, $attr )
    {
        $retValue = null;
        switch( $attr )
        {
            case 'handler':
            {
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

    function fetchHTTPInput( $http, $base, $event )
    {
        $selectionVar = 'CollaborationHandlerSelection_' . $event->attribute( 'id' );
        if ( $http->hasPostVariable( $selectionVar ) )
        {
            $selection = $http->postVariable( $selectionVar );
            $event->setAttribute( 'data_text1', implode( ',', $selection ) );
        }
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $object = eZContentObject::fetch( $parameters['object_id'] );

        $collaborationIdentifierList = $event->attribute( 'selection' );

        foreach ( $collaborationIdentifierList as $collaborationIdentifier )
        {
            $existing = eZCollaborationNotificationRule::fetchItemTypeList( $collaborationIdentifier, array( $object->attribute( 'id' ) ) );

            if ( count( $existing ) == 0 )
            {
                $rule = eZCollaborationNotificationRule::create( $collaborationIdentifier, $object->attribute( 'id' ) );
                $rule->store( );
            }
        }

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( 'addcollabrule', 'AddCollabRuleType' );

?>

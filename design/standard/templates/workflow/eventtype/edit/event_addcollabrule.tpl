{let $collabHandlers=$event.handler.collaboration_handlers}
<div class="block">
<p>{"Choose which collaboration items you wish to add notifications for."|i18n("design/admin/notification/collaboration")}</p>

{foreach $collabHandlers as $collabHandler}
    {let $types=$collabHandler.notification_types}
        {if or($types,$types|gt(0))}
            {if is_array($types)}
                <h3>{$collabHandler.info.type-name|wash}</h3>
                {foreach $types as $type}
                {let $typeIdentifer=concat($collabHandler.info.type-identifier, '_', $type.value)}
                    <input type="checkbox" name="CollaborationHandlerSelection_{$event.id}[]"
                        id="{$typeIdentifier}"
                        value="{$typeIdentifier}"
                        {if $event.selection|contains($typeIdentifier)}checked="checked"{/if} />
                    <label for="{$typeIdentifier}" style="display:inline;font-weight:normal;">{$type.name|wash}</label><br />
                {/let}
                {/foreach}
            {else}
               <input type="checkbox" name="CollaborationHandlerSelection_{$event.id}[]"
                    id="{$collabHandler.info.type-identifier}"
                    value="{$collabHandler.info.type-identifier}"
                    {if $event.selection|contains($collabHandler.info.type-identifier)}checked="checked"{/if} />
                    <label for="{$collabHandler.info.type-identifier}"  style="display:inline;font-weight:normal;">{$collabHandler.info.type-name|wash}</label><br />
            {/if}
        {/if}
    {/let}
{/foreach}

</div>
{/let}
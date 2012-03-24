<div class="element">
Rules to add:
 {foreach $event.selection as $collabNotificationIdentifier}
{delimiter}, {/delimiter}
{$collabNotificationIdentifier}
{/foreach}
</div>
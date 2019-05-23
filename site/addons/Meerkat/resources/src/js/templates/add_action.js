Meerkat.setMeerkatAddActionPartialTemplate(`
    <li v-if="item['published'] === false"><a href="#" @click.prevent="call('approveComment', item.id)" title="{{ translate('addons.Meerkat::actions.approve_desc') }}"><span class="icon icon-check"></span> {{ translate('addons.Meerkat::actions.approve') }}</a></li>
    <li v-if="item['published'] === true"><a href="#" @click.prevent="call('unApproveComment', item.id)" title="{{ translate('addons.Meerkat::actions.unapprove_desc') }}">{{ translate('addons.Meerkat::actions.unapprove') }}</a></li>
    <li><a href="#" @click.prevent="call('replyToComment', item.id)" title="{{ translate('addons.Meerkat::actions.reply_desc') }}"><span class="icon icon-reply"></span> {{ translate('addons.Meerkat::actions.reply') }}</a></li>
    <li><a href="#" @click.prevent="call('editComment', item.id)" title="{{ translate('addons.Meerkat::actions.edit_desc') }}"><span class="icon icon-edit"></span> {{ translate('addons.Meerkat::actions.edit') }}</a></li>
    <li v-if="item['spam'] === false"><a href="#" @click.prevent="call('markItemAsSpam', item.id)" title="{{ translate('addons.Meerkat::actions.spam_desc') }}"><span class="icon icon-shield"></span> {{ translate('addons.Meerkat::actions.spam') }}</a></li>
    <li v-if="item['spam'] === true"><a href="#" @click.prevent="call('markItemAsNotSpam', item.id)" title="{{ translate('addons.Meerkat::actions.not_spam_desc') }}"><span class="icon icon-shield"></span> {{ translate('addons.Meerkat::actions.not_spam') }}</a></li>
    <li><a href="#" @click.prevent="call('deleteItem', item.id)" title="{{ translate('addons.Meerkat::actions.delete_desc') }}"><span class="icon icon-trash"></span> {{ translate('addons.Meerkat::actions.delete') }}</a></li>
`);
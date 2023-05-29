<?php

namespace App\Permissions;

abstract class AdminPermission
{
    const DASHBOARD_READ = "admin.dashboard.read";

    const DELEGATOR_READ = "admin.delegators.read";
    const DELEGATOR_RECAP = "admin.delegators.recap";

    const PARTICIPANT_READ = "admin.participants.read";
    const PARTICIPANT_RECAP = "admin.participants.recap";
    const PARTICIPANT_IDCARD = "admin.participants.idcard";

    const PAYMENT_READ = "admin.payments.read";
    const PAYMENT_RECAP = "admin.payments.recap";

    const BROADCAST_READ = "admin.broadcast.read";
    const BROADCAST_REVISION = "admin.broadcast.revision";
    const BROADCAST_PAYMENT = "admin.broadcast.payment";

    const GUEST_READ = "admin.guest.read";
    const GUEST_INVITATION = "admin.guest.invitation";
    const ADD_DELETE = "admin.guest.add";
    const GUEST_DELETE = "admin.guest.delete";

    const EVENT_READ = "admin.event.read";
    const EVENT_ADD = "admin.event.add";
    const EVENT_DELETE = "admin.event.delete";
}
<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

/*
 * Each product and OAuth method: the PHP class it returns (checked against reflection) and the
 * contract's neutral name of that answer, for scripts/surface.php.
 */

return [
    'apps.authorizeUrl' => [
        'class' => 'Tawked\\Model\\OAuth\\AuthorizeUrl',
        'neutral' => 'AuthorizeUrl',
    ],
    'apps.exchangeCode' => [
        'class' => 'Tawked\\Model\\OAuth\\AppConnection',
        'neutral' => 'AppConnection',
    ],
    'chat.attendant.get' => [
        'class' => 'Tawked\\Model\\Chat\\Attendant',
        'neutral' => 'ChatAttendantGetResponse',
    ],
    'chat.attendant.publish' => [
        'class' => 'Tawked\\Model\\Chat\\ChatAttendantPublishResponse',
        'neutral' => 'ChatAttendantPublishResponse',
    ],
    'chat.attendant.saveDraft' => [
        'class' => 'Tawked\\Model\\Chat\\ChatAttendantDraftSaveResponse',
        'neutral' => 'ChatAttendantDraftSaveResponse',
    ],
    'chat.attendant.setState' => [
        'class' => 'Tawked\\Model\\Chat\\Attendant',
        'neutral' => 'ChatAttendantStateSetResponse',
    ],
    'chat.attendant.updateSettings' => [
        'class' => 'Tawked\\Model\\Chat\\Attendant',
        'neutral' => 'ChatAttendantSettingsUpdateResponse',
    ],
    'chat.menus.create' => [
        'class' => 'Tawked\\Model\\Chat\\ChatMenu',
        'neutral' => 'ChatMenuCreateResponse',
    ],
    'chat.menus.delete' => [
        'class' => 'Tawked\\Model\\Chat\\ChatMenuDeleteResponse',
        'neutral' => 'ChatMenuDeleteResponse',
    ],
    'chat.menus.get' => [
        'class' => 'Tawked\\Model\\Chat\\ChatMenu',
        'neutral' => 'ChatMenuGetResponse',
    ],
    'chat.menus.list' => [
        'class' => 'Tawked\\Model\\Chat\\ChatMenuListResponse',
        'neutral' => 'ChatMenuListResponse',
    ],
    'chat.menus.update' => [
        'class' => 'Tawked\\Model\\Chat\\ChatMenu',
        'neutral' => 'ChatMenuUpdateResponse',
    ],
    'email.checkKey' => [
        'class' => 'Tawked\\Model\\Email\\EmailKeyCheckResponse',
        'neutral' => 'EmailKeyCheckResponse',
    ],
    'email.domains.create' => [
        'class' => 'Tawked\\Model\\Email\\EmailDomain',
        'neutral' => 'EmailDomainCreateResponse',
    ],
    'email.domains.delete' => [
        'class' => 'Tawked\\Model\\Email\\EmailDomainDeleteResponse',
        'neutral' => 'EmailDomainDeleteResponse',
    ],
    'email.domains.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailDomain',
        'neutral' => 'EmailDomainGetResponse',
    ],
    'email.domains.list' => [
        'class' => 'Tawked\\Model\\Email\\EmailDomainListResponse',
        'neutral' => 'page<EmailDomain>',
        'item' => 'Tawked\\Model\\Email\\EmailDomain',
    ],
    'email.domains.update' => [
        'class' => 'Tawked\\Model\\Email\\EmailDomain',
        'neutral' => 'EmailDomainUpdateResponse',
    ],
    'email.domains.verify' => [
        'class' => 'Tawked\\Model\\Email\\EmailDomain',
        'neutral' => 'EmailDomainVerifyResponse',
    ],
    'email.inbound.addresses.list' => [
        'class' => 'Tawked\\Model\\Email\\EmailInboundAddressListResponse',
        'neutral' => 'EmailInboundAddressListResponse',
    ],
    'email.inbound.attachments.get' => [
        'class' => 'string',
        'neutral' => 'binary',
    ],
    'email.inbound.delete' => [
        'class' => 'Tawked\\Model\\Email\\EmailReceivedDeleteResponse',
        'neutral' => 'EmailReceivedDeleteResponse',
    ],
    'email.inbound.forward' => [
        'class' => 'Tawked\\Model\\Email\\EmailAccepted',
        'neutral' => 'EmailReceivedForwardResponse',
    ],
    'email.inbound.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailReceived',
        'neutral' => 'EmailReceivedGetResponse',
    ],
    'email.inbound.list' => [
        'class' => 'Tawked\\Model\\Email\\EmailReceivedListResponse',
        'neutral' => 'page<EmailReceivedListItem>',
        'item' => 'Tawked\\Model\\Email\\EmailReceivedListItem',
    ],
    'email.inbound.raw' => [
        'class' => 'string',
        'neutral' => 'binary',
    ],
    'email.inbound.test' => [
        'class' => 'Tawked\\Model\\Email\\EmailReceivedTestResponse',
        'neutral' => 'EmailReceivedTestResponse',
    ],
    'email.messages.cancel' => [
        'class' => 'Tawked\\Model\\Email\\EmailMessageExtended',
        'neutral' => 'EmailMessageCancelResponse',
    ],
    'email.messages.content' => [
        'class' => 'Tawked\\Model\\Email\\EmailMessageContentResponse',
        'neutral' => 'EmailMessageContentResponse',
    ],
    'email.messages.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailMessageGetResponse',
        'neutral' => 'EmailMessageGetResponse',
    ],
    'email.messages.list' => [
        'class' => 'Tawked\\Model\\Email\\EmailMessageListResponse',
        'neutral' => 'page<EmailMessageExtended>',
        'item' => 'Tawked\\Model\\Email\\EmailMessageExtended',
    ],
    'email.messages.send' => [
        'class' => 'Tawked\\Model\\Email\\EmailSendResponse',
        'neutral' => 'EmailSendResponse',
    ],
    'email.messages.sendBatch' => [
        'class' => 'Tawked\\Model\\Email\\EmailBatchSendResponse',
        'neutral' => 'EmailBatchSendResponse',
    ],
    'email.messages.update' => [
        'class' => 'Tawked\\Model\\Email\\EmailMessageExtended',
        'neutral' => 'EmailMessageRescheduleResponse',
    ],
    'email.metrics.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailMetrics',
        'neutral' => 'EmailMetricsGetResponse',
    ],
    'email.suppressions.add' => [
        'class' => 'Tawked\\Model\\Email\\EmailSuppressionAddResponse',
        'neutral' => 'EmailSuppressionAddResponse',
    ],
    'email.suppressions.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailSuppressionLookup',
        'neutral' => 'EmailSuppressionGetResponse',
    ],
    'email.suppressions.imports.create' => [
        'class' => 'Tawked\\Model\\Email\\EmailSuppressionImport',
        'neutral' => 'EmailSuppressionImportCreateResponse',
    ],
    'email.suppressions.imports.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailSuppressionImport',
        'neutral' => 'EmailSuppressionImportGetResponse',
    ],
    'email.suppressions.list' => [
        'class' => 'Tawked\\Model\\Email\\EmailSuppressionListResponse',
        'neutral' => 'page<EmailSuppression>',
        'item' => 'Tawked\\Model\\Email\\EmailSuppression',
    ],
    'email.suppressions.remove' => [
        'class' => 'Tawked\\Model\\Email\\EmailSuppressionRemoveResponse',
        'neutral' => 'EmailSuppressionRemoveResponse',
    ],
    'email.templates.create' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplate',
        'neutral' => 'EmailTemplateCreateResponse',
    ],
    'email.templates.delete' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplateDeleteResponse',
        'neutral' => 'EmailTemplateDeleteResponse',
    ],
    'email.templates.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplate',
        'neutral' => 'EmailTemplateGetResponse',
    ],
    'email.templates.list' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplateListResponse',
        'neutral' => 'page<EmailTemplate>',
        'item' => 'Tawked\\Model\\Email\\EmailTemplate',
    ],
    'email.templates.publish' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplate',
        'neutral' => 'EmailTemplatePublishResponse',
    ],
    'email.templates.update' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplate',
        'neutral' => 'EmailTemplateUpdateResponse',
    ],
    'email.templates.versions.get' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplateVersion',
        'neutral' => 'EmailTemplateVersionGetResponse',
    ],
    'email.templates.versions.list' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplateVersionListResponse',
        'neutral' => 'EmailTemplateVersionListResponse',
    ],
    'email.templates.versions.restore' => [
        'class' => 'Tawked\\Model\\Email\\EmailTemplate',
        'neutral' => 'EmailTemplateVersionRestoreResponse',
    ],
    'partner.applications.archive' => [
        'class' => 'Tawked\\Model\\Partner\\Application',
        'neutral' => 'PartnerApplicationArchiveResponse',
    ],
    'partner.applications.batch' => [
        'class' => 'Tawked\\Model\\Partner\\BatchResult',
        'neutral' => 'PartnerApplicationBatchResponse',
    ],
    'partner.applications.create' => [
        'class' => 'Tawked\\Model\\Partner\\ApplicationSummary',
        'neutral' => 'PartnerApplicationCreateResponse',
    ],
    'partner.applications.get' => [
        'class' => 'Tawked\\Model\\Partner\\Application',
        'neutral' => 'PartnerApplicationGetResponse',
    ],
    'partner.applications.history' => [
        'class' => 'Tawked\\Model\\Partner\\ApplicationHistory',
        'neutral' => 'PartnerApplicationHistoryResponse',
    ],
    'partner.applications.list' => [
        'class' => 'Tawked\\Model\\Partner\\ApplicationList',
        'neutral' => 'page<ApplicationRow>',
        'item' => 'Tawked\\Model\\Partner\\Application',
    ],
    'partner.applications.removeLogo' => [
        'class' => 'Tawked\\Model\\Partner\\Application',
        'neutral' => 'PartnerLogoRemoveResponse',
    ],
    'partner.applications.restore' => [
        'class' => 'Tawked\\Model\\Partner\\Application',
        'neutral' => 'PartnerApplicationRestoreResponse',
    ],
    'partner.applications.resume' => [
        'class' => 'Tawked\\Model\\Partner\\ApplicationStateChange',
        'neutral' => 'PartnerApplicationResumeResponse',
    ],
    'partner.applications.suspend' => [
        'class' => 'Tawked\\Model\\Partner\\ApplicationStateChange',
        'neutral' => 'PartnerApplicationSuspendResponse',
    ],
    'partner.applications.unpause' => [
        'class' => 'Tawked\\Model\\Partner\\ApplicationStateChange',
        'neutral' => 'PartnerApplicationUnpauseResponse',
    ],
    'partner.applications.update' => [
        'class' => 'Tawked\\Model\\Partner\\ApplicationSummary',
        'neutral' => 'PartnerApplicationUpdateResponse',
    ],
    'partner.applications.updateVerifySettings' => [
        'class' => 'Tawked\\Model\\Partner\\VerifySettingsResult',
        'neutral' => 'PartnerVerifySettingsUpdateResponse',
    ],
    'partner.applications.uploadLogo' => [
        'class' => 'Tawked\\Model\\Partner\\Application',
        'neutral' => 'PartnerLogoUploadResponse',
    ],
    'partner.events.list' => [
        'class' => 'Tawked\\Model\\Partner\\EventPage',
        'neutral' => 'page<PartnerEventListItem>',
        'item' => 'Tawked\\Model\\Partner\\PartnerEvent',
    ],
    'partner.profile.get' => [
        'class' => 'Tawked\\Model\\Partner\\Profile',
        'neutral' => 'PartnerProfileResponse',
    ],
    'partner.screening.check' => [
        'class' => 'Tawked\\Model\\Partner\\ScreeningResult',
        'neutral' => 'PartnerScreeningResponse',
    ],
    'partner.usage.get' => [
        'class' => 'Tawked\\Model\\Partner\\Usage',
        'neutral' => 'PartnerUsageResponse',
    ],
    'partner.verifications.list' => [
        'class' => 'Tawked\\Model\\Partner\\VerificationPage',
        'neutral' => 'page<PartnerVerificationListItem>',
        'item' => 'Tawked\\Model\\Partner\\PartnerVerification',
    ],
    'partner.webhook.deliveries.list' => [
        'class' => 'Tawked\\Model\\Partner\\WebhookDeliveryPage',
        'neutral' => 'page<WebhookDelivery>',
        'item' => 'Tawked\\Model\\Partner\\WebhookDelivery',
    ],
    'partner.webhook.deliveries.retry' => [
        'class' => 'Tawked\\Model\\Partner\\WebhookDelivery',
        'neutral' => 'PartnerDeliveryRetryResponse',
    ],
    'partner.webhook.get' => [
        'class' => 'Tawked\\Model\\Partner\\PartnerWebhook',
        'neutral' => 'PartnerWebhookGetResponse',
    ],
    'partner.webhook.update' => [
        'class' => 'Tawked\\Model\\Partner\\PartnerWebhook',
        'neutral' => 'PartnerWebhookUpdateResponse',
    ],
    'verify.cancel' => [
        'class' => 'Tawked\\Model\\Verify\\VerificationCanceled',
        'neutral' => 'VerifyCancelResponse',
    ],
    'verify.check' => [
        'class' => 'Tawked\\Model\\Verify\\CheckResult',
        'neutral' => 'VerifyCheckResponse',
    ],
    'verify.get' => [
        'class' => 'Tawked\\Model\\Verify\\Verification',
        'neutral' => 'VerifyGetResponse',
    ],
    'verify.resend' => [
        'class' => 'Tawked\\Model\\Verify\\VerificationResent',
        'neutral' => 'VerifyResendResponse',
    ],
    'verify.start' => [
        'class' => 'Tawked\\Model\\Verify\\VerificationStarted',
        'neutral' => 'VerifyStartResponse',
    ],
    'webhookEndpoints.create' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookEndpoint',
        'neutral' => 'WebhookEndpointCreateResponse',
    ],
    'webhookEndpoints.delete' => [
        'class' => 'Tawked\\Model\\WebhookEndpoints\\WebhookEndpointDeleteResponse',
        'neutral' => 'WebhookEndpointDeleteResponse',
    ],
    'webhookEndpoints.deliveries.get' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookEndpointDelivery',
        'neutral' => 'WebhookDeliveryGetResponse',
    ],
    'webhookEndpoints.deliveries.list' => [
        'class' => 'Tawked\\Model\\WebhookEndpoints\\WebhookDeliveryListResponse',
        'neutral' => 'page<WebhookEndpointDelivery>',
        'item' => 'Tawked\\Model\\Webhook\\WebhookEndpointDelivery',
    ],
    'webhookEndpoints.deliveries.replay' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookEndpointDelivery',
        'neutral' => 'WebhookDeliveryReplayResponse',
    ],
    'webhookEndpoints.get' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookEndpoint',
        'neutral' => 'WebhookEndpointGetResponse',
    ],
    'webhookEndpoints.list' => [
        'class' => 'Tawked\\Model\\WebhookEndpoints\\WebhookEndpointListResponse',
        'neutral' => 'WebhookEndpointListResponse',
    ],
    'webhookEndpoints.replay' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookReplay',
        'neutral' => 'WebhookReplayCreateResponse',
    ],
    'webhookEndpoints.replays.get' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookReplay',
        'neutral' => 'WebhookReplayGetResponse',
    ],
    'webhookEndpoints.rotateSecret' => [
        'class' => 'Tawked\\Model\\WebhookEndpoints\\WebhookSecretRotateResponse',
        'neutral' => 'WebhookSecretRotateResponse',
    ],
    'webhookEndpoints.test' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookEndpointDelivery',
        'neutral' => 'WebhookTestSendResponse',
    ],
    'webhookEndpoints.update' => [
        'class' => 'Tawked\\Model\\Webhook\\WebhookEndpoint',
        'neutral' => 'WebhookEndpointUpdateResponse',
    ],
    'whatsapp.messages.get' => [
        'class' => 'Tawked\\Model\\WhatsApp\\Message',
        'neutral' => 'WhatsAppMessageGetResponse',
    ],
    'whatsapp.messages.send' => [
        'class' => 'Tawked\\Model\\WhatsApp\\SentMessage',
        'neutral' => 'WhatsAppSendResponse',
    ],
    'whatsapp.templates.create' => [
        'class' => 'Tawked\\Model\\WhatsApp\\CreatedTemplate',
        'neutral' => 'WhatsAppTemplateCreateResponse',
    ],
    'whatsapp.templates.delete' => [
        'class' => 'Tawked\\Model\\WhatsApp\\DeletedTemplate',
        'neutral' => 'WhatsAppTemplateDeleteResponse',
    ],
    'whatsapp.templates.get' => [
        'class' => 'Tawked\\Model\\WhatsApp\\Template',
        'neutral' => 'WhatsAppTemplateGetResponse',
    ],
    'whatsapp.templates.list' => [
        'class' => 'Tawked\\Model\\WhatsApp\\TemplateList',
        'neutral' => 'WhatsAppTemplateListResponse',
    ],
    'whatsapp.templates.update' => [
        'class' => 'Tawked\\Model\\WhatsApp\\Template',
        'neutral' => 'WhatsAppTemplateUpdateResponse',
    ],
];

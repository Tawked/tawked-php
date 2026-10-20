<?php

/*
 * The PHP class names the generator (scripts/generate.php) gives the contract's objects, kept by
 * hand so today's public names stay ("the same class names and properties
 * where they exist"). Everything not named here takes the generator's rule:
 *
 * - an endpoint's answer: the class of its `sdk.response.type` when it has one, else
 *   `<Group>\<type_base>Response` (`Email\EmailSendResponse`);
 * - an object inside another: `<Parent><Property>` (singular for a list's items);
 * - a contract type: `types` below (a type missing there stops the run).
 */

declare(strict_types=1);

return [
    // Contract type => class under Tawked\Model. Two types may share a class: their properties merge.
    'types' => [
        'VerifySettings' => 'Partner\\VerifySettings',
        'Screening' => 'Partner\\Screening',
        'ApplicationSummary' => 'Partner\\ApplicationSummary',
        'ApplicationRow' => 'Partner\\Application',
        'Verification' => 'Verify\\Verification',
        'WhatsAppMessage' => 'WhatsApp\\Message',
        'WhatsAppTemplate' => 'WhatsApp\\Template',
        'VerificationEventData' => 'Webhook\\VerificationEventData',
        'PartnerVerificationEventData' => 'Webhook\\VerificationEventData',
        'MessageEventData' => 'Webhook\\MessageEventData',
        'EmailMessage' => 'Email\\EmailMessage',
        'EmailMessageExtended' => 'Email\\EmailMessageExtended',
        'EmailAccepted' => 'Email\\EmailAccepted',
        'EmailRecipient' => 'Email\\EmailRecipient',
        'EmailRecipientAccepted' => 'Email\\EmailRecipientAccepted',
        'EmailCost' => 'Email\\EmailCost',
        'EmailTag' => 'Email\\EmailTag',
        'EmailError' => 'Email\\EmailError',
        'EmailBounce' => 'Email\\EmailBounce',
        'EmailBatchError' => 'Email\\EmailBatchError',
        'ChatMenu' => 'Chat\\ChatMenu',
        'Attendant' => 'Chat\\Attendant',
        'AttendantMenu' => 'Chat\\AttendantMenu',
        'AttendantChoice' => 'Chat\\AttendantChoice',
        'AttendantMenuInput' => 'Chat\\AttendantMenuInput',
        'AttendantChoiceInput' => 'Chat\\AttendantChoiceInput',
        'AttendantProblem' => 'Chat\\AttendantProblem',
        'EmailEventData' => 'Webhook\\EmailEventData',
        'EmailRecipientEventData' => 'Webhook\\EmailRecipientEventData',
        'WebhookDelivery' => 'Partner\\WebhookDelivery',
        'PartnerWebhook' => 'Partner\\PartnerWebhook',
        // Tawked Email's management API and an application's webhook endpoints.
        'EmailDomain' => 'Email\\EmailDomain',
        'EmailSuppression' => 'Email\\EmailSuppression',
        'EmailSuppressionLookup' => 'Email\\EmailSuppressionLookup',
        'EmailSuppressionImport' => 'Email\\EmailSuppressionImport',
        'EmailMetrics' => 'Email\\EmailMetrics',
        'EmailTemplate' => 'Email\\EmailTemplate',
        'EmailTemplateVersion' => 'Email\\EmailTemplateVersion',
        'EmailTemplateVersionRow' => 'Email\\EmailTemplateVersionRow',
        'EmailTemplateProblem' => 'Email\\EmailTemplateProblem',
        'WebhookEndpoint' => 'Webhook\\WebhookEndpoint',
        'WebhookEndpointDelivery' => 'Webhook\\WebhookEndpointDelivery',
        'WebhookReplay' => 'Webhook\\WebhookReplay',
        'DomainEventData' => 'Webhook\\DomainEventData',
        'SuppressionAddedEventData' => 'Webhook\\SuppressionAddedEventData',
        'SuppressionRemovedEventData' => 'Webhook\\SuppressionRemovedEventData',
        // Received e-mail.
        'EmailReceived' => 'Email\\EmailReceived',
        'EmailReceivedListItem' => 'Email\\EmailReceivedListItem',
        'EmailReceivedAttachment' => 'Email\\EmailReceivedAttachment',
        'EmailInboundAddress' => 'Email\\EmailInboundAddress',
        'EmailReceivedEventData' => 'Webhook\\EmailReceivedEventData',
    ],

    // Endpoint id => the class of its answer, where today's name differs from the rule.
    'responses' => [
        'start' => 'Verify\\VerificationStarted',
        'check' => 'Verify\\CheckResult',
        'resend' => 'Verify\\VerificationResent',
        'cancel' => 'Verify\\VerificationCanceled',
        'whatsapp-send' => 'WhatsApp\\SentMessage',
        'whatsapp-templates' => 'WhatsApp\\TemplateList',
        'whatsapp-template-create' => 'WhatsApp\\CreatedTemplate',
        'whatsapp-template-delete' => 'WhatsApp\\DeletedTemplate',
        'partner-profile' => 'Partner\\Profile',
        'partner-screening' => 'Partner\\ScreeningResult',
        'partner-batch' => 'Partner\\BatchResult',
        'partner-list' => 'Partner\\ApplicationList',
        'partner-history' => 'Partner\\ApplicationHistory',
        'partner-verify-settings' => 'Partner\\VerifySettingsResult',
        'partner-suspend' => 'Partner\\ApplicationStateChange',
        'partner-resume' => 'Partner\\ApplicationStateChange',
        'partner-unpause' => 'Partner\\ApplicationStateChange',
        'partner-usage' => 'Partner\\Usage',
        'partner-verifications' => 'Partner\\VerificationPage',
        'partner-events' => 'Partner\\EventPage',
        'partner-deliveries' => 'Partner\\WebhookDeliveryPage',
    ],

    // "<Parent class short name>.<property>" => the class of the object (or of a list's items) inside.
    'nested' => [
        'Message.error' => 'MessageError',
        'MessageEventData.error' => 'WhatsApp\\MessageError',
        'Template.placeholders' => 'TemplatePlaceholders',
        'TemplateSummary.placeholders' => 'TemplatePlaceholders',
        'TemplatePlaceholders.buttons' => 'TemplateButton',
        'Template.components' => 'TemplateComponent',
        'TemplateComponent.buttons' => 'TemplateComponentButton',
        'TemplateList.data' => 'TemplateSummary',
        'ApplicationHistory.events' => 'HistoryEvent',
        'BatchResult.results' => 'BatchItem',
        'Usage.applications' => 'UsageRow',
        'VerificationPage.data' => 'PartnerVerification',
        'EventPage.data' => 'PartnerEvent',
        'PartnerEvent.delivery' => 'EventDelivery',
        'ScreeningResult.review' => 'ScreeningReview',
        'EmailAccepted.from' => 'EmailAddress',
        'EmailMessage.from' => 'EmailAddress',
        'EmailMessageExtended.from' => 'EmailAddress',
        'EmailMessageGetResponse.from' => 'EmailAddress',
        'EmailSendResponse.from' => 'EmailAddress',
        'EmailEventData.from' => 'Email\\EmailAddress',
        'EmailRecipientEventData.from' => 'Email\\EmailAddress',
        'EmailMessage.attachments' => 'EmailAttachment',
        'EmailMessageExtended.attachments' => 'EmailAttachment',
        'EmailMessageGetResponse.attachments' => 'EmailAttachment',
        'EmailEventData.bounce' => 'Email\\EmailBounce',
        'EmailRecipientEventData.bounce' => 'Email\\EmailBounce',
        'EmailEventData.error' => 'Email\\EmailError',
        'EmailRecipientEventData.error' => 'Email\\EmailError',
        'AttendantChoiceInput.action' => 'AttendantChoiceAction',
        'AttendantChoiceAction.assign' => 'AttendantChoiceActionAssign',
    ],

    // "<Class short name>.<wire property>" => its PHP property, where camelCase would take a name every
    // model already has (`raw`, `meta`): a received e-mail's `raw` (the raw message's download link).
    'properties' => [
        'EmailReceived.raw' => 'rawMessage',
    ],

    // Webhook event data classes for the partner events, which the contract writes inline.
    'events' => [
        'service.' => 'Webhook\\ApplicationEventData',
        'balance.low' => 'Webhook\\BalanceLowData',
    ],

    // Namespace segment => its PHP spelling, where ucfirst() is not it.
    'segments' => [
        'whatsapp' => 'WhatsApp',
        'integration_apps' => 'IntegrationApps',
    ],
];

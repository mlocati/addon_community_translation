<?php

namespace CommunityTranslation\Notification\Category;

use CommunityTranslation\Entity\Notification as NotificationEntity;
use CommunityTranslation\Notification\Category;
use CommunityTranslation\Repository\Locale as LocaleRepository;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Exception;

/**
 * Notification category: the request of a new locale has been approved.
 */
class NewLocaleApproved extends Category
{
    /**
     * @var int
     */
    const PRIORITY = 10;

    /**
     * {@inheritdoc}
     *
     * @see Category::getRecipientIDs()
     */
    protected function getRecipientIDs(NotificationEntity $notification)
    {
        $result = [];
        $notificationData = $notification->getNotificationData();
        $locale = $this->app->make(LocaleRepository::class)->findApproved($notificationData['localeID']);
        if ($locale === null) {
            throw new Exception(t('Unable to find the locale with ID %s', $notificationData['localeID']));
        }
        if ($locale->getRequestedBy() !== null) {
            $result[] = $locale->getRequestedBy()->getUserID();
        }
        $group = $this->getGroupsHelper()->getGlobalAdministrators();
        $result = array_merge($result, $group->getGroupMemberIDs());

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see Category::getMailParameters()
     */
    public function getMailParameters(NotificationEntity $notification, UserInfo $recipient)
    {
        $notificationData = $notification->getNotificationData();
        $locale = $this->app->make(LocaleRepository::class)->findApproved($notificationData['localeID']);
        if ($locale === null) {
            throw new Exception(t('Unable to find the locale with ID %s', $notificationData['localeID']));
        }
        $requestedBy = $locale->getRequestedBy();
        $approvedBy = $notificationData['by'] ? $this->app->make(UserInfoRepository::class)->getByID($notificationData['by']) : null;

        return [
            'localeName' => $locale->getDisplayName(),
            'requestedBy' => $requestedBy,
            'approvedBy' => $approvedBy,
            'teamsUrl' => $this->getBlockPageURL('CommunityTranslation Team List', 'details/' . $locale->getID()),
        ] + $this->getCommonMailParameters($notification, $recipient);
    }
}

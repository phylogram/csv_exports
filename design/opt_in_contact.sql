SELECT *
  FROM redhen_contact
  JOIN (campaignion_activity, campaignion_activity_newsletter_subscription)
    ON (redhen_contact.contact_id = campaignion_activity.contact_id
	   AND campaignion_activity.activity_id = campaignion_activity_newsletter_subscription.activity_id
        )

 
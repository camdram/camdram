Feature: Signing up for a Camdram account
  In order perform non-readonly actions on Camdram
  I need to be able to create a user account

Scenario: I successfully sign up for an account
  Given I am on "/auth/create-account"
  And I fill in the following:
      | Name                                          | New User             |
      | acts_camdrambundle_usertype_email             | new.user@camdram.net |
      | acts_camdrambundle_usertype_password_password | testpassword         |
      | Confirm password                              | testpassword         |
      | acts_camdrambundle_usertype_occupation        | No                   |
      | acts_camdrambundle_usertype_graduation        | 2012                 |
  And I press "Register"
  Then I should see "New User" in the "#account-link" element

Scenario: I fill out the form incorrectly
  Given I am on "/auth/create-account"
  And I fill in the following:
    | Name                                          | New User             |
    | acts_camdrambundle_usertype_email             | new.user@camdram.net |
    | acts_camdrambundle_usertype_password_password | testpassword         |
    | Confirm password                              | testpassword_???     |
    | acts_camdrambundle_usertype_occupation        | No                   |
    | acts_camdrambundle_usertype_graduation        | 2012                 |
  And I press "Register"
  Then I should be on "/auth/create-account"
  And I should see a "#acts_camdrambundle_usertype_password_password.error" element
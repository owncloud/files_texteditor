Feature: loginz

	Scenario: simple user login
		Given a regular user exists
		And I am on the loginz page
		When I loginz as a regular user with a correct password
		Then I should be redirected to a page with the title "Files - ownCloud"

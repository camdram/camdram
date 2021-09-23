Cypress.Commands.add('loadTestFixtures', () => {
    cy.exec('php app/console camdram:database:refresh --env=test');
});

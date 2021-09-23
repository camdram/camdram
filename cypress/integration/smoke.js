describe('Smoke test key pages for zero JS errors', () => {
    before(() => {
        cy.loadTestFixtures();
    });

    it('Visit key pages and ensure no JS errors', () => {
        const urls = [
            '/',
            '/diary',
            '/venues/',
        ];

        for (const url of urls) {
            cy.visit(url);
        }
    });
})

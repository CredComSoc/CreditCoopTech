
const url = "155.4.159.231:8080"

describe('My First Test', () => {
    it('Does not do much!', () => {
      expect(true).to.equal(true)
    })
  })
  

  describe('Login', () => {
    it('Login to SB web app', () => {
      cy.visit(url)

      cy.get('input[id="email-input"]').click().type("User")

      cy.get('input[id="password-input"]').click().type("123")

      cy.get('Button').click()
    })
  })
  
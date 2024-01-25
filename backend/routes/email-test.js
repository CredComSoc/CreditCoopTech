const express = require('express');
const { marked } = require('marked');
const handlebars = require("handlebars");
const nodemailer = require('nodemailer');
//const config = require('../config');
const fs = require('fs').promises;

const app = express();
const port = 3000;

marked.Renderer.prototype.paragraph = (text) => {
  return text + '\n';
};

const support_email="support@landcaretrade.com";
const support_email_password="=_Gy!;aUtR;Z2Ck";
//const support_email = process.env.SUPPORT_EMAIL;
//const support_email_password = process.env.SUPPORT_EMAIL_PASSWORD;
//const support_email = config.SUPPORT_EMAIL
//const support_email_password = config.SUPPORT_EMAIL_PASSWORD

app.get('/send-email/', async (req, res) => {
  const { templateName } = req.params;

  try {
    // Read the Markdown template
    const markdownTemplate = await fs.readFile('email-templates.md', 'utf-8');

    // Extract the desired template based on the Template tag
    const templateSections = extractTemplates(markdownTemplate);
    const selectedTemplate_1 = templateSections['WelcomeSubject'];
    const selectedTemplate_2 = templateSections['WelcomeBody'];

    if (!selectedTemplate_1 || !selectedTemplate_2) {
      return res.status(404).send('Template not found');
    }

    //const templateData = {
    //  FRONTEND_URL: 'http://localhost:3000',
    //  token: 'xxx-xxxx-xxx'
    //}
    const templateData = {
      FRONTEND_URL: 'http://localhost:3000',
      email: 'jmuthui70@gmail.com',
      password: 'joepassword3'
    }
    const bodyTemplate = compileTemplate(selectedTemplate_2, templateData);
    // Convert Markdown to HTML
    const htmlTemplate_1 = marked(selectedTemplate_1);
    const htmlTemplate_2 = marked(bodyTemplate);
    //htmlTemplate_1.replace(/^(?:<p>)?(.*?)(?:<\/p>)?$/, "$1");
    htmlTemplate_1.replace('<p>', '');
    // Send the HTML template in an email
    await sendEmail(htmlTemplate_2, 'jmuthui70@gmail.com', htmlTemplate_1);

    res.send('Email sent successfully');
  } catch (error) {
    console.error('Error sending email:', error);
    res.status(500).send('Internal Server Error');
  }
});

// Function to replace variables in the template
function compileTemplate(template, data) {
  const compiledTemplate = handlebars.compile(template);
  return compiledTemplate(data);
}

// Function to extract templates based on Template tags
function extractTemplates(markdownContent) {
  const templateSections = {};
  const lines = markdownContent.split('\n');

  let currentTemplate = '';
  for (const line of lines) {
    const templateTagMatch = line.match(/<!-- Template: (.+) -->/);
    if (templateTagMatch) {
      currentTemplate = templateTagMatch[1];
      templateSections[currentTemplate] = '';
    } else {
      templateSections[currentTemplate] += line + '\n';
    }
  }

  return templateSections;
}

// Function to send emails
async function sendEmail(htmlContent, to, subject) {
  const transporter = nodemailer.createTransport({
    // configure your email transport options (SMTP, Gmail, etc.)
      host: 'smtp.migadu.com',
      port: 587,
      secure: false,
      auth: {
        user: support_email,
        pass: support_email_password
      }
  });

  const mailOptions = {
    from: support_email,
    to,
    subject,
    html: htmlContent,
  };

  return transporter.sendMail(mailOptions);
}

app.listen(port, () => {
  console.log(`Server is running on http://localhost:${port}`);
});

import { Controller } from '@hotwired/stimulus'

/**
 * HelloController is responsible for displaying messages in the view.
 * It demonstrates the use of Stimulus targets, actions, and values.
 */
export default class extends Controller {
    // Declare targets and values used by this controller
    static targets = ['message']
    static values = { content: String }

    // When the controller is connected, display the default or provided message
    connect() {
        // Display a default message if no content value is provided
        this.showMessage(this.contentValue || 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js');
    }

    /**
     * Updates the messageTarget's text content with the provided content.
     * @param {string} content - The content to display in the target element.
     */
    showMessage(content) {
        this.messageTarget.textContent = content;
    }

    // Action triggered when the "Greet" button is clicked
    greet() {
        this.showMessage('Hello from Stimulus!');
    }

    /**
     * Action to change the content dynamically from another part of the page
     * Can be triggered by any event, such as a button click
     * @param {Event} event - The event object from the user interaction
     */
    changeContent(event) {
        const newContent = event.target.getAttribute('data-new-content');
        if (newContent) {
            this.showMessage(newContent);
        } else {
            this.showMessage('Default message if no data-new-content is found');
        }
    }
}

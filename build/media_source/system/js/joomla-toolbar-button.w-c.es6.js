/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

window.customElements.define('joomla-toolbar-button', class extends HTMLElement {
  // Attribute getters
  get task() { return this.getAttribute('task'); }

  get listSelection() { return this.hasAttribute('list-selection'); }

  get form() { return this.getAttribute('form'); }

  get formValidation() { return this.hasAttribute('form-validation'); }

  get confirmMessage() { return this.getAttribute('confirm-message'); }

  /**
   * Lifecycle
   */
  constructor() {
    super();

    if (!Joomla) {
      throw new Error('Joomla API is not properly initiated');
    }
    
    this.onChange = this.onChange.bind(this);

    this.addEventListener('click', event => this.executeTask(event));
  }

  /**
   * Lifecycle
   */
  connectedCallback() {
    // We need a button to support button behavior,
    // because we cannot currently extend HTMLButtonElement
    this.buttonElement = this.querySelector('button, a');

    // Check whether we have a form
    const formSelector = this.form || 'adminForm';
    this.formElement = document.getElementById(formSelector);

    this.disabled = false;
    // If list selection is required, set button to disabled by default
    if (this.listSelection) {
      this.setDisabled(true);
    }

    if (this.listSelection) {
      if (!this.formElement) {
        throw new Error(`The form "${formSelector}" is required to perform the task, but the form was not found on the page.`);
      }

      // Watch on list selection
      this.formElement.boxchecked.addEventListener('change', this.onChange);
    }
  }

  /**
   * Lifecycle
   */
  disconnectedCallback() {
    if (this.formElement.boxchecked) {
      this.formElement.boxchecked.removeEventListener('change', this.onChange);
    }

    this.removeEventListener('click');
  }

  onChange(event) {
    // Check whether we have selected something
    this.setDisabled(event.target.value < 1);
  }

  setDisabled(disabled) {
    // Make sure we have a boolean value
    this.disabled = !!disabled;

    // Switch attribute for current element
    if (this.disabled) {
      this.setAttribute('disabled', true);
    } else {
      this.removeAttribute('disabled');
    }

    // Switch attribute for native element
    // An anchor does not support "disabled" attribute, so use class
    if (this.buttonElement) {
      if (this.disabled) {
        if (this.buttonElement.nodeName === 'BUTTON') {
          this.buttonElement.setAttribute('disabled', true);
        } else {
          this.buttonElement.classList.add('disabled');
        }
      } else if (this.buttonElement.nodeName === 'BUTTON') {
        this.buttonElement.removeAttribute('disabled');
      } else {
        this.buttonElement.classList.remove('disabled');
      }
    }
  }

  executeTask() {
    if (this.disabled) {
      return false;
    }

    // eslint-disable-next-line no-restricted-globals
    if (this.confirmMessage && !confirm(this.confirmMessage)) {
      return false;
    }

    if (this.task) {
      Joomla.submitbutton(this.task, this.form, this.formValidation);
    }

    return true;
  }
});
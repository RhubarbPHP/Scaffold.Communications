var communicationItemCollectionViewBridge = function (leafPath) {
    window.rhubarb.viewBridgeClasses.ViewBridge.apply(this, arguments);
};

communicationItemCollectionViewBridge.prototype = new window.rhubarb.viewBridgeClasses.ViewBridge();
communicationItemCollectionViewBridge.prototype.constructor = communicationItemCollectionViewBridge;

communicationItemCollectionViewBridge.prototype.attachEvents = function () {
    var self = this;

    var contentDialog = this.viewNode.querySelector('.js-content-modal');

    if (!contentDialog) {
        // Avoid JS errors if the expected dialog isn't available
        console.log('Communication content dialog element not found');
        return;
    }

    this.viewNode.onclick = function (event) {
        if (!/(^|\s)view-content-button($|\s)/.exec(event.target.className)) {
            return;
        }

        event.preventDefault();

        contentDialog.querySelector('.c-modal__body').innerHTML = 'Loading...';
        contentDialog.style.display = 'block';

        var id = event.target.getAttribute('data-id');

        self.raiseServerEvent('getContentForCommunicationItem', id, function (response) {
            contentDialog.querySelector('.c-modal__body').innerHTML = response;
        });

        return false;
    };

    this.viewNode.querySelector('.js-modal-close').onclick = function (event) {
        event.preventDefault();
        contentDialog.style.display = 'none';
        return false;
    };
};

window.rhubarb.viewBridgeClasses.CommunicationItemCollectionViewBridge = communicationItemCollectionViewBridge;

chrome.contextMenus.create({
	'title': 'Add to Calendar',
    'contexts': ['all'],
	'onclick': function() {
        chrome.tabs.getSelected(function(tab) {
            chrome.tabs.sendMessage(tab.id, "get_selection", function(response) {
                console.log(response);
            });
        });
	}
});
chrome.contextMenus.create({
	'title': 'Add to Tasks',
    'contexts': ['all'],
	'onclick': function() {
        chrome.tabs.getSelected(function(tab) {
            chrome.tabs.sendMessage(tab.id, "get_and_insert");
        });
	}
});
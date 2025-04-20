const Azirax = {
    treeJson: {
        "tree": {
            "l": 0, "n": "", "p": "", "c": [{
                "l": 1, "n": "Azirax", "p": "Azirax", "c": [{
                    "l": 2,
                    "n": "Documentation",
                    "p": "Azirax/Documentation",
                    "c": [{
                        "l": 3,
                        "n": "Console",
                        "p": "Azirax/Documentation/Console",
                        "c": [{
                            "l": 4,
                            "n": "Command",
                            "p": "Azirax/Documentation/Console/Command",
                            "c": [{
                                "l": 5,
                                "n": "Command",
                                "p": "Azirax/Documentation/Console/Command/Command"
                            }, {
                                "l": 5,
                                "n": "ParseCommand",
                                "p": "Azirax/Documentation/Console/Command/ParseCommand"
                            }, {
                                "l": 5,
                                "n": "RenderCommand",
                                "p": "Azirax/Documentation/Console/Command/RenderCommand"
                            }]
                        }, {
                            "l": 4,
                            "n": "Output",
                            "p": "Azirax/Documentation/Console/Output",
                            "c": [{
                                "l": 5,
                                "n": "SymfonyOutput",
                                "p": "Azirax/Documentation/Console/Output/SymfonyOutput"
                            }]
                        }, {"l": 4, "n": "Application", "p": "Azirax/Documentation/Console/Application"}]
                    }, {
                        "l": 3,
                        "n": "Parser",
                        "p": "Azirax/Documentation/Parser",
                        "c": [{
                            "l": 4,
                            "n": "ClassVisitor",
                            "p": "Azirax/Documentation/Parser/ClassVisitor",
                            "c": [{
                                "l": 5,
                                "n": "InheritdocClassVisitor",
                                "p": "Azirax/Documentation/Parser/ClassVisitor/InheritdocClassVisitor"
                            }, {
                                "l": 5,
                                "n": "MethodClassVisitor",
                                "p": "Azirax/Documentation/Parser/ClassVisitor/MethodClassVisitor"
                            }, {
                                "l": 5,
                                "n": "PropertyClassVisitor",
                                "p": "Azirax/Documentation/Parser/ClassVisitor/PropertyClassVisitor"
                            }, {
                                "l": 5,
                                "n": "ViewSourceClassVisitor",
                                "p": "Azirax/Documentation/Parser/ClassVisitor/ViewSourceClassVisitor"
                            }]
                        }, {
                            "l": 4,
                            "n": "Filters",
                            "p": "Azirax/Documentation/Parser/Filters",
                            "c": [{
                                "l": 5,
                                "n": "AllFilter",
                                "p": "Azirax/Documentation/Parser/Filters/AllFilter"
                            }, {
                                "l": 5,
                                "n": "DefaultFilter",
                                "p": "Azirax/Documentation/Parser/Filters/DefaultFilter"
                            }, {
                                "l": 5,
                                "n": "FilterInterface",
                                "p": "Azirax/Documentation/Parser/Filters/FilterInterface"
                            }, {"l": 5, "n": "PublicFilter", "p": "Azirax/Documentation/Parser/Filters/PublicFilter"}]
                        }, {
                            "l": 4,
                            "n": "FunctionVisitor",
                            "p": "Azirax/Documentation/Parser/FunctionVisitor",
                            "c": [{
                                "l": 5,
                                "n": "ViewSourceFunctionVisitor",
                                "p": "Azirax/Documentation/Parser/FunctionVisitor/ViewSourceFunctionVisitor"
                            }]
                        }, {
                            "l": 4,
                            "n": "Node",
                            "p": "Azirax/Documentation/Parser/Node",
                            "c": [{"l": 5, "n": "DocBlockNode", "p": "Azirax/Documentation/Parser/Node/DocBlockNode"}]
                        }, {
                            "l": 4,
                            "n": "ClassVisitorInterface",
                            "p": "Azirax/Documentation/Parser/ClassVisitorInterface"
                        }, {"l": 4, "n": "CodeParser", "p": "Azirax/Documentation/Parser/CodeParser"}, {
                            "l": 4,
                            "n": "DocBlockParser",
                            "p": "Azirax/Documentation/Parser/DocBlockParser"
                        }, {
                            "l": 4,
                            "n": "FunctionVisitorInterface",
                            "p": "Azirax/Documentation/Parser/FunctionVisitorInterface"
                        }, {"l": 4, "n": "NodeVisitor", "p": "Azirax/Documentation/Parser/NodeVisitor"}, {
                            "l": 4,
                            "n": "Parser",
                            "p": "Azirax/Documentation/Parser/Parser"
                        }, {"l": 4, "n": "ParserContext", "p": "Azirax/Documentation/Parser/ParserContext"}, {
                            "l": 4,
                            "n": "ParserError",
                            "p": "Azirax/Documentation/Parser/ParserError"
                        }, {
                            "l": 4,
                            "n": "ProjectTraverser",
                            "p": "Azirax/Documentation/Parser/ProjectTraverser"
                        }, {"l": 4, "n": "Transaction", "p": "Azirax/Documentation/Parser/Transaction"}]
                    }, {
                        "l": 3,
                        "n": "Providers",
                        "p": "Azirax/Documentation/Providers",
                        "c": [{
                            "l": 4,
                            "n": "CodeParserProvider",
                            "p": "Azirax/Documentation/Providers/CodeParserProvider"
                        }, {
                            "l": 4,
                            "n": "DocBlockParserProvider",
                            "p": "Azirax/Documentation/Providers/DocBlockParserProvider"
                        }, {
                            "l": 4,
                            "n": "FilterProvider",
                            "p": "Azirax/Documentation/Providers/FilterProvider"
                        }, {
                            "l": 4,
                            "n": "IndexerProvider",
                            "p": "Azirax/Documentation/Providers/IndexerProvider"
                        }, {
                            "l": 4,
                            "n": "ParserContextProvider",
                            "p": "Azirax/Documentation/Providers/ParserContextProvider"
                        }, {
                            "l": 4,
                            "n": "ParserProvider",
                            "p": "Azirax/Documentation/Providers/ParserProvider"
                        }, {
                            "l": 4,
                            "n": "PhpParserProvider",
                            "p": "Azirax/Documentation/Providers/PhpParserProvider"
                        }, {
                            "l": 4,
                            "n": "PhpTraverserProvider",
                            "p": "Azirax/Documentation/Providers/PhpTraverserProvider"
                        }, {
                            "l": 4,
                            "n": "PrettyPrinterProvider",
                            "p": "Azirax/Documentation/Providers/PrettyPrinterProvider"
                        }, {
                            "l": 4,
                            "n": "ProjectProvider",
                            "p": "Azirax/Documentation/Providers/ProjectProvider"
                        }, {
                            "l": 4,
                            "n": "RendererProvider",
                            "p": "Azirax/Documentation/Providers/RendererProvider"
                        }, {
                            "l": 4,
                            "n": "ServiceProviderInterface",
                            "p": "Azirax/Documentation/Providers/ServiceProviderInterface"
                        }, {"l": 4, "n": "StoreProvider", "p": "Azirax/Documentation/Providers/StoreProvider"}, {
                            "l": 4,
                            "n": "ThemesProvider",
                            "p": "Azirax/Documentation/Providers/ThemesProvider"
                        }, {
                            "l": 4,
                            "n": "TraverserProvider",
                            "p": "Azirax/Documentation/Providers/TraverserProvider"
                        }, {"l": 4, "n": "TreeProvider", "p": "Azirax/Documentation/Providers/TreeProvider"}, {
                            "l": 4,
                            "n": "TwigProvider",
                            "p": "Azirax/Documentation/Providers/TwigProvider"
                        }, {"l": 4, "n": "VersionsProvider", "p": "Azirax/Documentation/Providers/VersionsProvider"}]
                    }, {
                        "l": 3, "n": "Reflection", "p": "Azirax/Documentation/Reflection", "c": [{
                            "l": 4,
                            "n": "Interfaces",
                            "p": "Azirax/Documentation/Reflection/Interfaces",
                            "c": [{
                                "l": 5,
                                "n": "ArrayInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/ArrayInterface"
                            }, {
                                "l": 5,
                                "n": "ClassReflectionInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/ClassReflectionInterface"
                            }, {
                                "l": 5,
                                "n": "ConstantsReflectionInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/ConstantsReflectionInterface"
                            }, {
                                "l": 5,
                                "n": "DocumentationInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/DocumentationInterface"
                            }, {
                                "l": 5,
                                "n": "FunctionReflectionInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/FunctionReflectionInterface"
                            }, {
                                "l": 5,
                                "n": "HintsInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/HintsInterface"
                            }, {
                                "l": 5,
                                "n": "InjectClassInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/InjectClassInterface"
                            }, {
                                "l": 5,
                                "n": "MethodReflectionInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/MethodReflectionInterface"
                            }, {
                                "l": 5,
                                "n": "ModifierInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/ModifierInterface"
                            }, {
                                "l": 5,
                                "n": "ParameterReflectionInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/ParameterReflectionInterface"
                            }, {
                                "l": 5,
                                "n": "PropertyReflectionInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/PropertyReflectionInterface"
                            }, {
                                "l": 5,
                                "n": "ReflectionInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/ReflectionInterface"
                            }, {
                                "l": 5,
                                "n": "TagsInterface",
                                "p": "Azirax/Documentation/Reflection/Interfaces/TagsInterface"
                            }]
                        }, {
                            "l": 4,
                            "n": "Traits",
                            "p": "Azirax/Documentation/Reflection/Traits",
                            "c": [{
                                "l": 5,
                                "n": "ClassTrait",
                                "p": "Azirax/Documentation/Reflection/Traits/ClassTrait"
                            }, {
                                "l": 5,
                                "n": "DocumentationTrait",
                                "p": "Azirax/Documentation/Reflection/Traits/DocumentationTrait"
                            }, {
                                "l": 5,
                                "n": "HintsTrait",
                                "p": "Azirax/Documentation/Reflection/Traits/HintsTrait"
                            }, {
                                "l": 5,
                                "n": "ModifierTrait",
                                "p": "Azirax/Documentation/Reflection/Traits/ModifierTrait"
                            }, {"l": 5, "n": "TagsTrait", "p": "Azirax/Documentation/Reflection/Traits/TagsTrait"}]
                        }, {
                            "l": 4,
                            "n": "ClassReflection",
                            "p": "Azirax/Documentation/Reflection/ClassReflection"
                        }, {
                            "l": 4,
                            "n": "ConstantsReflection",
                            "p": "Azirax/Documentation/Reflection/ConstantsReflection"
                        }, {
                            "l": 4,
                            "n": "FunctionReflection",
                            "p": "Azirax/Documentation/Reflection/FunctionReflection"
                        }, {
                            "l": 4,
                            "n": "HintReflection",
                            "p": "Azirax/Documentation/Reflection/HintReflection"
                        }, {
                            "l": 4,
                            "n": "LazyClassReflection",
                            "p": "Azirax/Documentation/Reflection/LazyClassReflection"
                        }, {
                            "l": 4,
                            "n": "MethodReflection",
                            "p": "Azirax/Documentation/Reflection/MethodReflection"
                        }, {
                            "l": 4,
                            "n": "ParameterReflection",
                            "p": "Azirax/Documentation/Reflection/ParameterReflection"
                        }, {
                            "l": 4,
                            "n": "PropertyReflection",
                            "p": "Azirax/Documentation/Reflection/PropertyReflection"
                        }, {"l": 4, "n": "Reflection", "p": "Azirax/Documentation/Reflection/Reflection"}]
                    }, {
                        "l": 3,
                        "n": "RemoteRepository",
                        "p": "Azirax/Documentation/RemoteRepository",
                        "c": [{
                            "l": 4,
                            "n": "AbstractRemoteRepository",
                            "p": "Azirax/Documentation/RemoteRepository/AbstractRemoteRepository"
                        }, {
                            "l": 4,
                            "n": "BitBucketRemoteRepository",
                            "p": "Azirax/Documentation/RemoteRepository/BitBucketRemoteRepository"
                        }, {
                            "l": 4,
                            "n": "GitHubRemoteRepository",
                            "p": "Azirax/Documentation/RemoteRepository/GitHubRemoteRepository"
                        }, {
                            "l": 4,
                            "n": "GitLabRemoteRepository",
                            "p": "Azirax/Documentation/RemoteRepository/GitLabRemoteRepository"
                        }]
                    }, {
                        "l": 3,
                        "n": "Renderer",
                        "p": "Azirax/Documentation/Renderer",
                        "c": [{"l": 4, "n": "Diff", "p": "Azirax/Documentation/Renderer/Diff"}, {
                            "l": 4,
                            "n": "Index",
                            "p": "Azirax/Documentation/Renderer/Index"
                        }, {"l": 4, "n": "Renderer", "p": "Azirax/Documentation/Renderer/Renderer"}, {
                            "l": 4,
                            "n": "Theme",
                            "p": "Azirax/Documentation/Renderer/Theme"
                        }, {"l": 4, "n": "ThemeSet", "p": "Azirax/Documentation/Renderer/ThemeSet"}, {
                            "l": 4,
                            "n": "TwigExtension",
                            "p": "Azirax/Documentation/Renderer/TwigExtension"
                        }]
                    }, {
                        "l": 3,
                        "n": "Store",
                        "p": "Azirax/Documentation/Store",
                        "c": [{"l": 4, "n": "ArrayStore", "p": "Azirax/Documentation/Store/ArrayStore"}, {
                            "l": 4,
                            "n": "JsonStore",
                            "p": "Azirax/Documentation/Store/JsonStore"
                        }, {"l": 4, "n": "StoreInterface", "p": "Azirax/Documentation/Store/StoreInterface"}]
                    }, {
                        "l": 3,
                        "n": "Version",
                        "p": "Azirax/Documentation/Version",
                        "c": [{
                            "l": 4,
                            "n": "GitVersionCollection",
                            "p": "Azirax/Documentation/Version/GitVersionCollection"
                        }, {
                            "l": 4,
                            "n": "SingleVersionCollection",
                            "p": "Azirax/Documentation/Version/SingleVersionCollection"
                        }, {"l": 4, "n": "Version", "p": "Azirax/Documentation/Version/Version"}, {
                            "l": 4,
                            "n": "VersionCollection",
                            "p": "Azirax/Documentation/Version/VersionCollection"
                        }]
                    }, {"l": 3, "n": "Azirax", "p": "Azirax/Documentation/Azirax"}, {
                        "l": 3,
                        "n": "ErrorHandler",
                        "p": "Azirax/Documentation/ErrorHandler"
                    }, {"l": 3, "n": "Indexer", "p": "Azirax/Documentation/Indexer"}, {
                        "l": 3,
                        "n": "Message",
                        "p": "Azirax/Documentation/Message"
                    }, {"l": 3, "n": "Project", "p": "Azirax/Documentation/Project"}, {
                        "l": 3,
                        "n": "Tree",
                        "p": "Azirax/Documentation/Tree"
                    }, {"l": 3, "n": "TreeNode", "p": "Azirax/Documentation/TreeNode"}]
                }]
            }]
        }, "treeOpenLevel": null
    },
    /** @var boolean */
    treeLoaded: false,
    /** @var boolean */
    listenersRegistered: false,
    autoCompleteData: null,
    /** @var boolean */
    autoCompleteLoading: false,
    /** @var boolean */
    autoCompleteLoaded: false,
    /** @var string|null */
    rootPath: null,
    /** @var string|null */
    autoCompleteDataUrl: null,
    /** @var HTMLElement|null */
    aziraxSearchAutoComplete: null,
    /** @var HTMLElement|null */
    aziraxSearchAutoCompleteProgressBarContainer: null,
    /** @var HTMLElement|null */
    aziraxSearchAutoCompleteProgressBar: null,
    /** @var number */
    aziraxSearchAutoCompleteProgressBarPercent: 0,
    /** @var autoComplete|null */
    autoCompleteJS: null,
    querySearchSecurityRegex: /([^0-9a-zA-Z:\\\\_\s])/gi,
    buildTreeNode: function (treeNode, htmlNode, treeOpenLevel) {
        let ulNode = document.createElement('ul');
        for (let childKey in treeNode.c) {
            let child = treeNode.c[childKey];
            let liClass = document.createElement('li');
            const hasChildren = child.hasOwnProperty('c');
            let nodeSpecialName = (hasChildren ? 'namespace:' : 'class:') + child.p.replace(/\//g, '_');
            liClass.setAttribute('data-name', nodeSpecialName);

            // Create the node that will have the text
            let divHd = document.createElement('div');
            const levelCss = child.l - 1;
            divHd.className = hasChildren ? 'hd' : 'hd leaf';
            divHd.style.paddingLeft = (hasChildren ? (levelCss * 18) : (8 + (levelCss * 18))) + 'px';
            if (hasChildren) {
                if (child.l <= treeOpenLevel) {
                    liClass.className = 'opened';
                }
                let spanIcon = document.createElement('span');
                spanIcon.setAttribute('uk-icon', 'icon: triangle-right');
                divHd.appendChild(spanIcon);
            }
            let aLink = document.createElement('a');

            // Edit the HTML link to work correctly based on the current depth
            aLink.href = Azirax.rootPath + child.p + '.html';
            aLink.innerText = child.n;
            divHd.appendChild(aLink);
            liClass.appendChild(divHd);

            // It has children
            if (hasChildren) {
                let divBd = document.createElement('div');
                divBd.className = 'bd';
                Azirax.buildTreeNode(child, divBd, treeOpenLevel);
                liClass.appendChild(divBd);
            }
            ulNode.appendChild(liClass);
        }
        htmlNode.appendChild(ulNode);
    },
    initListeners: function () {
        if (Azirax.listenersRegistered) {
            // Quick exit, already registered
            return;
        }
        Azirax.listenersRegistered = true;
    },
    loadTree: function () {
        if (Azirax.treeLoaded) {
            // Quick exit, already registered
            return;
        }
        Azirax.rootPath = document.body.getAttribute('data-root-path');
        Azirax.buildTreeNode(Azirax.treeJson.tree, document.getElementById('api-tree'), Azirax.treeJson.treeOpenLevel);

        // Toggle left-nav divs on click
        $('#api-tree .hd span').on('click', function () {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        const expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            let container = $('#api-tree');
            let node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                let scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }
        Azirax.treeLoaded = true;
    },
    pagePartiallyLoaded: function (event) {
        Azirax.initListeners();
        Azirax.loadTree();
        Azirax.loadAutoComplete();
    },
    pageFullyLoaded: function (event) {
        // it may not have received DOMContentLoaded event
        Azirax.initListeners();
        Azirax.loadTree();
        Azirax.loadAutoComplete();
        // Fire the event in the search page too
        if (typeof AziraxSearch === 'object') {
            AziraxSearch.pageFullyLoaded();
        }
    },
    loadAutoComplete: function () {
        if (Azirax.autoCompleteLoaded) {
            // Quick exit, already loaded
            return;
        }
        Azirax.autoCompleteDataUrl = document.body.getAttribute('data-search-index-url');
        Azirax.aziraxSearchAutoComplete = document.getElementById('azirax-search-auto-complete');
        Azirax.aziraxSearchAutoCompleteProgressBarContainer = document.getElementById('search-progress-bar-container');
        Azirax.aziraxSearchAutoCompleteProgressBar = document.getElementById('search-progress-bar');
        if (Azirax.aziraxSearchAutoComplete !== null) {
            // Wait for it to be loaded
            Azirax.aziraxSearchAutoComplete.addEventListener('init', function (_) {
                Azirax.autoCompleteLoaded = true;
                Azirax.aziraxSearchAutoComplete.addEventListener('selection', function (event) {
                    // Go to selection page
                    window.location = Azirax.rootPath + event.detail.selection.value.p;
                });
                Azirax.aziraxSearchAutoComplete.addEventListener('navigate', function (event) {
                    // Set selection in text box
                    if (typeof event.detail.selection.value === 'object') {
                        Azirax.aziraxSearchAutoComplete.value = event.detail.selection.value.n;
                    }
                });
                Azirax.aziraxSearchAutoComplete.addEventListener('results', function (event) {
                    Azirax.markProgressFinished();
                });
            });
        }
        // Check if the lib is loaded
        if (typeof autoComplete === 'function') {
            Azirax.bootAutoComplete();
        }
    },
    markInProgress: function () {
        Azirax.aziraxSearchAutoCompleteProgressBarContainer.className = 'search-bar';
        Azirax.aziraxSearchAutoCompleteProgressBar.className = 'progress-bar indeterminate';
        if (typeof AziraxSearch === 'object' && AziraxSearch.pageFullyLoaded) {
            AziraxSearch.aziraxSearchPageAutoCompleteProgressBarContainer.className = 'search-bar';
            AziraxSearch.aziraxSearchPageAutoCompleteProgressBar.className = 'progress-bar indeterminate';
        }
    },
    markProgressFinished: function () {
        Azirax.aziraxSearchAutoCompleteProgressBarContainer.className = 'search-bar hidden';
        Azirax.aziraxSearchAutoCompleteProgressBar.className = 'progress-bar';
        if (typeof AziraxSearch === 'object' && AziraxSearch.pageFullyLoaded) {
            AziraxSearch.aziraxSearchPageAutoCompleteProgressBarContainer.className = 'search-bar hidden';
            AziraxSearch.aziraxSearchPageAutoCompleteProgressBar.className = 'progress-bar';
        }
    },
    makeProgress: function () {
        Azirax.makeProgressOnProgressBar(
            Azirax.aziraxSearchAutoCompleteProgressBarPercent,
            Azirax.aziraxSearchAutoCompleteProgressBar
        );
        if (typeof AziraxSearch === 'object' && AziraxSearch.pageFullyLoaded) {
            Azirax.makeProgressOnProgressBar(
                Azirax.aziraxSearchAutoCompleteProgressBarPercent,
                AziraxSearch.aziraxSearchPageAutoCompleteProgressBar
            );
        }
    },
    loadAutoCompleteData: function (query) {
        return new Promise(function (resolve, reject) {
            if (Azirax.autoCompleteData !== null) {
                resolve(Azirax.autoCompleteData);
                return;
            }
            Azirax.markInProgress();

            function reqListener() {
                Azirax.autoCompleteLoading = false;
                Azirax.autoCompleteData = JSON.parse(this.responseText).items;
                Azirax.markProgressFinished();

                setTimeout(function () {
                    resolve(Azirax.autoCompleteData);
                }, 50);// Let the UI render once before sending the results for processing. This gives time to the progress bar to hide
            }

            function reqError(err) {
                Azirax.autoCompleteLoading = false;
                Azirax.autoCompleteData = null;
                console.error(err);
                reject(err);
            }

            let oReq = new XMLHttpRequest();
            oReq.onload = reqListener;
            oReq.onerror = reqError;
            oReq.onprogress = function (pe) {
                if (pe.lengthComputable) {
                    Azirax.aziraxSearchAutoCompleteProgressBarPercent = parseInt(pe.loaded / pe.total * 100, 10);
                    Azirax.makeProgress();
                }
            };
            oReq.onloadend = function (_) {
                Azirax.markProgressFinished();
            };
            oReq.open('get', Azirax.autoCompleteDataUrl, true);
            oReq.send();
        });
    },
    /**
     * Make some progress on a progress bar
     *
     * @param number percentage
     * @param HTMLElement progressBar
     * @return void
     */
    makeProgressOnProgressBar: function (percentage, progressBar) {
        progressBar.className = 'progress-bar';
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute(
            'aria-valuenow', percentage
        );
    },
    searchEngine: function (query, record) {
        if (typeof query !== 'string') {
            return '';
        }
        // replace all (mode = g) spaces and non breaking spaces (\s) by pipes
        // g = global mode to mark also the second word searched
        // i = case insensitive
        // how this function works:
        // First: search if the query has the keywords in sequence
        // Second: replace the keywords by a mark and leave all the text in between non marked

        if (record.match(new RegExp('(' + query.replace(/\s/g, ').*(') + ')', 'gi')) === null) {
            return '';// Does not match
        }

        const replacedRecord = record.replace(new RegExp('(' + query.replace(/\s/g, '|') + ')', 'gi'), function (group) {
            return '<mark class="auto-complete-highlight">' + group + '</mark>';
        });

        if (replacedRecord !== record) {
            return replacedRecord;// This should not happen but just in case there was no match done
        }

        return '';
    },
    /**
     * Clean the search query
     *
     * @param string|null query
     * @return string
     */
    cleanSearchQuery: function (query) {
        if (typeof query !== 'string') {
            return '';
        }
        // replace any chars that could lead to injecting code in our regex
        // remove start or end spaces
        // replace backslashes by an escaped version, use case in search: \myRootFunction
        return query.replace(Azirax.querySearchSecurityRegex, '').trim().replace(/\\/g, '\\\\');
    },
    bootAutoComplete: function () {
        Azirax.autoCompleteJS = new autoComplete(
            {
                selector: '#azirax-search-auto-complete',
                searchEngine: function (query, record) {
                    return Azirax.searchEngine(query, record);
                },
                submit: true,
                data: {
                    src: function (q) {
                        Azirax.markInProgress();
                        return Azirax.loadAutoCompleteData(q);
                    },
                    keys: ['n'],// Data 'Object' key to be searched
                    cache: false, // Is not compatible with async fetch of data
                },
                query: (input) => {
                    return Azirax.cleanSearchQuery(input);
                },
                trigger: (query) => {
                    return Azirax.cleanSearchQuery(query).length > 0;
                },
                resultsList: {
                    tag: 'ul',
                    class: 'auto-complete-dropdown-menu',
                    destination: '#auto-complete-results',
                    position: 'afterbegin',
                    maxResults: 500,
                    noResults: false,
                },
                resultItem: {
                    tag: 'li',
                    class: 'auto-complete-result',
                    highlight: 'auto-complete-highlight',
                    selected: 'auto-complete-selected'
                },
            }
        );
    }
};


document.addEventListener('DOMContentLoaded', Azirax.pagePartiallyLoaded, false);
window.addEventListener('load', Azirax.pageFullyLoaded, false);

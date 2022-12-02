///////////////////////////////////////////////////
//// Board Creation methods

function createBoard (tokens, blocker, blockers, colorArray, terrainArray, boardArray) {
    const boardContainer = document.getElementById("boardContainer");
    for (let i=0;i<20; i++) { // increment through every boardID
        let space = createSpace(i, terrainArray[i], boardArray); // create the space
        boardContainer.appendChild(space); // append the space
        for(let j=0;j<tokens.length; j++) { // increment through every player
            if (tokens[j][1] == i) { // if a player is on that space
                appendToken(i, tokens[j][2], false, colorArray[j]) // append a token
            }
        }
    }
    if (blocker != 6) {
        for (let k=0;k<4;k++){ // increment through all 4 quads
            appendToken(blockers[k][1], blockers[k][0], true, "000000"); // append blocker token
        }
    }
}

function createSpace (boardID, terrain, boardArray) {
    // create Space container
    let space = document.createElement("div"); 
    space.id="space_"+boardID;
    let spaceP = document.createElement("p");
    spaceP.id = "text_space_"+boardID;
    spaceP.classList.add("spaceText");
    space.classList.add('space');
    space.classList.add(terrain);
    let spacePtext = document.createTextNode("Space "+(boardID+1));
    spaceP.appendChild(spacePtext);
    space.appendChild(spaceP);

    // create Resource block
    let spaceResource = document.createElement("div");
    spaceResource.id = "resourceContainer_space_"+boardID;
    spaceResource.classList.add("resourceContainer");

    // create Az resources
    let resourceA = document.createElement("div");
    resourceA.id = "resources_a_space_"+boardID;
    resourceA.classList.add("resource");
    let iconA = document.createElement("div");
    iconA.id = "resources_a_icon_space_"+boardID;
    iconA.classList.add("resourceIconA");
    let resourceAText = document.createElement("p");
    resourceAText.id = "resources_a_text_space_"+boardID;
    resourceAText.classList.add("resourceText");
    let Atext = document.createTextNode(boardArray[boardID]['Az']);
    resourceAText.appendChild(Atext);
    resourceA.appendChild(resourceAText);
    resourceA.appendChild(iconA);

    // create Cath resources
    let resourceC = document.createElement("div");
    resourceC.id = "resources_c_space_"+boardID;
    resourceC.classList.add("resource");
    let iconC = document.createElement("div");
    iconC.id = "resources_c_icon_space_"+boardID;
    iconC.classList.add("resourceIconC");
    let resourceCText = document.createElement("p");
    resourceCText.id = "resources_c_text_space_"+boardID;
    resourceCText.classList.add("resourceText");
    let Ctext = document.createTextNode(boardArray[boardID]['Cath']);
    resourceCText.appendChild(Ctext);
    resourceC.appendChild(resourceCText);
    resourceC.appendChild(iconC);

    // create People resources
    let resourceP = document.createElement("div");
    resourceP.id = "resources_p_space_"+boardID;
    resourceP.classList.add("resource");
    let iconP = document.createElement("div");
    iconP.id = "resources_p_icon_space_"+boardID;
    iconP.classList.add("resourceIconP");
    let resourcePText = document.createElement("p");
    resourcePText.id = "resources_p_text_space_"+boardID;
    resourcePText.classList.add("resourceText");
    let Ptext = document.createTextNode(boardArray[boardID]['People']);
    resourcePText.appendChild(Ptext);
    resourceP.appendChild(resourcePText);
    resourceP.appendChild(iconP);

    // create Token container
    let tokenContainer = document.createElement("div");
    tokenContainer.id = "tokenContainer_"+boardID;
    tokenContainer.classList.add("tokenContainer");

    // put it all together
    spaceResource.appendChild(resourceA);
    spaceResource.appendChild(resourceC);
    spaceResource.appendChild(resourceP);
    space.appendChild(spaceResource);
    space.appendChild(tokenContainer);
    
    return space;    
}

function appendToken (boardID, tokenID, blocker, color) {
    let containerID = "tokenContainer_"+boardID;
    const playerContainer = document.getElementById(containerID); // get container
    let addToken = document.createElement("div"); // create div

    if (blocker == false) { // If player
        addToken.id="token_"+tokenID; // add ID
        addToken.classList.add("token"); // add token class
        tokenColor = "filter_" +color; 
        addToken.classList.add(tokenColor);  
    } else { // if blocker
        addToken.classList.add("blocker"); // add blocker class
        addToken.classList.add("filter_000000"); // add color
        addToken.id = "blocker_"+tokenID; // add ID
    }
    playerContainer.appendChild(addToken); // append
}

function clearBoard () { // in case I need to clear the whole board
    for (let i=0;i<20; i++) { // increment through every space
       const element = document.getElementById("space_"+i); // get the space
       if (element) { // if it exists
            element.remove(); // remove it
       }
    }
}

function createTemple (aLevel, cLevel, yFlag, aFlag, cFlag) {
    templeContainer = document.getElementById("temple"); // get the main temple container
    
    // create Apoc status
    let apocFlagContainer = document.createElement("div"); // create apocFlagContainer
    apocFlagContainer.id = "apocFlagContainer"; // assign id
    apocFlagContainer.classList.add("apocFlagContainer"); // add class
    
    let apocFlagDiv = document.createElement("div"); // create flag
    apocFlagDiv.id = "apocFlag"; // assign id
    apocFlagContainer.classList.add("apocFlag"); // add class

    apocFlagContainer.appendChild(apocFlagDiv); // append child
    templeContainer.appendChild(apocFlagContainer); // append child
    

    // create Temple tracks
    let templeTracksContainer = document.createElement("div"); // create track container
    templeTracksContainer.id = "templeTracksContainer"; // assign id
    templeContainer.classList.add("templeTracksContainer"); // add class

    // Aztec temple
    let azTempleTrack = document.createElement("div"); // create Az track 
    azTempleTrack.id = "azTemple"; // assign id
    azTempleTrack.classList.add("templeTrack"); // add class

    // aztec header
    let azTempleTitle = document.createElement("p");
    azTempleTitle.id = "azTempleTitle"; // assign id
    azTempleTitle.classList.add("trackHeader"); // add class
    let aHeaderText = document.createTextNode("Aztec Figure Direction"); // Aztect Temple title text
    azTempleTitle.appendChild(aHeaderText);
    azTempleTrack.appendChild(azTempleTitle);

    // Cath temple
    let cathTempleTrack = document.createElement("div"); // create Cath track 
    cathTempleTrack.id = "cathTemple"; // assign id
    cathTempleTrack.classList.add("templeTrack"); // add class

    // Cath header
    let cathTempleTitle = document.createElement("p");
    cathTempleTitle.id = "cathTempleTitle"; // assign id
    cathTempleTitle.classList.add("trackHeader"); // add class
    let cHeaderText = document.createTextNode("Cath Figure Direction"); // Cath Temple title text
    cathTempleTitle.appendChild(cHeaderText);
    cathTempleTrack.appendChild(cathTempleTitle);

    // put it all together
    templeTracksContainer.appendChild(azTempleTrack);
    templeTracksContainer.appendChild(cathTempleTrack);
    templeContainer.appendChild(templeTracksContainer);

    // populate the tracks

    // Aztec track
    for (let i=6;i>=0;i--) { // loop through each track
        let elem = document.createElement("div") // create element
        elem.id = "az_"+i; // assign id
        elem.classList.add("trackSpace"); // add class
        azTempleTrack.appendChild(elem); // append
        if (i == aLevel) {
            appendTemple(i, "A");
        }
    }
    // Cath track
    for (let i=6;i>=0;i--) { // loop through each track
        let elem = document.createElement("div") // create element
        elem.id = "cath_"+i; // assign id
        elem.classList.add("trackSpace"); // add class
        cathTempleTrack.appendChild(elem); // append
        if (i == cLevel) {
            appendTemple(i, "C");
        }
    }

    // set flags
    setFlags("Y", yFlag);
    setFlags("A", aFlag);
    setFlags("C", cFlag);
    
} 

function appendTemple (trackSpace, type) {
    let templeToken = document.createElement("div");
    templeToken.classList.add("templeToken");
    
    switch (type) {
        case "A":
            templeToken.id= "azTempleToken";
            trackSpace = document.getElementById("az_"+trackSpace);
        break;
        case "C":
            templeToken.id= "cathTempleToken";
            trackSpace = document.getElementById("cath_"+trackSpace);
        break;
    }
    trackSpace.appendChild(templeToken);
}

function setFlags (type, flag) {
    switch (type) {
        case "Y":
            if (flag == 0) {
                apocFlag = document.getElementById("apocFlagContainer");
                apocTitle = document.createElement("p");
                apocTitle = document.createTextNode("Apocalypse Status: No Apocalypse");
                apocFlag.appendChild(apocTitle);
            } else if (flag == 1) {
                apocFlag = document.getElementById("apocFlagContainer");
                apocTitle = document.createElement("p");
                apocTitle = document.createTextNode("Apocalypse Status: Aztec Apocalypse");
                apocFlag.appendChild(apocTitle);
            } else if(flag == 2) {
                apocFlag = document.getElementById("apocFlagContainer");
                apocTitle = document.createElement("p");
                apocTitle = document.createTextNode("Apocalypse Status: Aztec Apocalypse");
                apocFlag.appendChild(apocTitle);
            } else {
                apocFlag = document.getElementById("apocFlagContainer");
                apocTitle = document.createElement("p");
                apocTitle = document.createTextNode("Flag Error");
                apocFlag.appendChild(apocTitle);
                console.error("wrong apoc value")
            }
        break;
        case "A":
            if(flag == true) {
                templeFlag = document.createElement("div");
                templeFlag.id= "azFlag";
                templeFlag.classList.add("downArrow");
                azTempleLevel = document.getElementById("azTempleTitle");
                azTempleLevel.appendChild(templeFlag);
            } else if(flag == false) {
                templeFlag = document.createElement("div");
                templeFlag.id= "azFlag";
                templeFlag.classList.add("upArrow");
                azTempleFlag = document.getElementById("azTempleTitle");
                azTempleFlag.appendChild(templeFlag);
            } else {
                templeFlag = document.getElementById("azFlag")
                if (templeFlag) {
                    templeFlag.remove();
                }
                console.error("az flag error");
            }
        break;
        case "C":
            if(flag == true) {
                templeFlag = document.createElement("div");
                templeFlag.id= "cathFlag";
                templeFlag.classList.add("downArrow");
                cathTempleLevel = document.getElementById("cathTempleTitle");
                cathTempleLevel.appendChild(templeFlag);
            } else if(flag == false) {
                templeFlag = document.createElement("div");
                templeFlag.id= "cathFlag";
                templeFlag.classList.add("upArrow");
                cathTempleFlag = document.getElementById("cathTempleTitle");
                cathTempleFlag.appendChild(templeFlag);
            } else {
                templeFlag = document.getElementById("cathFlag")
                if (templeFlag) {
                    templeFlag.remove();
                }
                console.error("az flag error");
            }                    
        break;
    }
}

function removePossible() {
    for(let i=0;i<20; i++) {
        let removeTags = document.getElementById("space_"+i);
        if(removeTags.classList.contains("possibleMove")) {
            removeTags.classList.remove("possibleMove");
        }
        if(removeTags.classList.contains("blockedMove")) {
            removeTags.classList.remove("blockedMove");
        }
    }
}


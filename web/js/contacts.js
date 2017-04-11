var limitNum = 1000; // Limite de caractères

// Fonction lors d'un event KeyUp
function limitTextKeyUp(limitField, limitCount) {
	if (limitField.value.length > limitNum) { // Si la longeur du texte est plus grande que la limite
		limitField.value = limitField.value.substring(0, limitNum); // On met à jour le texte seulement pour le nombre de caractères permit
	} else {
		limitCount.value = limitNum - limitField.value.length; // Sinon on met à jour le compteur
	}
}

// Fonction lors d'un event KeyDown
function limitTextKeDown(limitField,limitCount){
	if (limitField.value.length > limitNum) { // Si la longeur du texte est plus grande que la limite
		limitField.value = limitField.value.substring(0, limitNum); // On met à jour le texte seulement pour le nombre de caractères permit
	} else {
		limitCount.value = limitNum - limitField.value.length; // Sinon on met à jour le compteur
	}
}
document.addEventListener("DOMContentLoaded", function () {
    const domtomPostalCodes = {
        "Guadeloupe": { start: 97100, end: 97190, id_country: domtom_country_ids.GP },
        "Martinique": { start: 97200, end: 97290, id_country: domtom_country_ids.MQ },
        "Guyane française": { start: 97300, end: 97390, id_country: domtom_country_ids.GF },
        "La Réunion": { start: 97400, end: 97490, id_country: domtom_country_ids.RE },
        "Mayotte": { start: 97600, end: 97690, id_country: domtom_country_ids.YT },
        "Saint-Pierre-et-Miquelon": { start: 97500, end: 97590, id_country: domtom_country_ids.PM },
        "Nouvelle-Calédonie": { start: 98800, end: 98890, id_country: domtom_country_ids.NC },
        "Polynésie française": { start: 98700, end: 98790, id_country: domtom_country_ids.PF },
        "Wallis-et-Futuna": { start: 98600, end: 98690, id_country: domtom_country_ids.WF },
        "Terres australes et antarctiques françaises": { start: 98400, end: 98490, id_country: domtom_country_ids.TA },
        "Saint-Barthélemy": { start: 97700, end: 97790, id_country: domtom_country_ids.BL },
        "Saint-Martin": { start: 97800, end: 97890, id_country: domtom_country_ids.MF }
    };


    function getInputs() {
        return {
            postalInput: document.querySelector("input[name='postcode']"),
            countrySelect: document.querySelector("select[name='id_country']")
        };
    }

    /**
     * Affichage des erreurs
     * @param message
     */
    function showError(message) {
        const { countrySelect } = getInputs();
        const formGroup = countrySelect.closest(".form-group.row");
        if (!formGroup) return;
        formGroup.classList.add("has-error");
        let existingError = formGroup.querySelector(".help-block");
        if (existingError) existingError.remove();
        const errorBlock = document.createElement("div");
        errorBlock.className = "help-block";
        errorBlock.innerHTML = `
            <ul>
                <li class="alert alert-danger">${message}</li>
            </ul>
        `;

        const customSelect2 = formGroup.querySelector(".custom-select2");
        if (customSelect2) {
            customSelect2.insertAdjacentElement("afterend", errorBlock);
        } else {
            const customSelect = formGroup.querySelector('[name=id_country]');
            if (customSelect) {
                customSelect.insertAdjacentElement("afterend", errorBlock);
            }
        }
    }

    function clearError() {
        const { countrySelect } = getInputs();
        const formGroup = countrySelect.closest(".form-group.row");
        if (!formGroup) return;

        formGroup.classList.remove("has-error");

        let existingError = formGroup.querySelector(".help-block");
        if (existingError) existingError.remove();
    }

    function validateCountryWithPostalCode() {
        const { postalInput, countrySelect } = getInputs();

        if (!postalInput || !countrySelect) return;

        const postalCode = parseInt(postalInput.value, 10);
        const selectedCountryId = parseInt(countrySelect.value, 10);

        let errorMessage = "";

        for (const [region, data] of Object.entries(domtomPostalCodes)) {
            if (postalCode >= data.start && postalCode <= data.end) {
                if (selectedCountryId !== data.id_country) {
                    errorMessage = `⚠️ Le code postal ${postalCode} correspond à ${region}. Veuillez sélectionner "${region}" comme pays.`;
                }
                break;
            }
        }

        if (errorMessage) {
            showError(errorMessage);
        } else {
            clearError();
        }
    }

    function attachValidation() {
        const { postalInput, countrySelect } = getInputs();

        if (postalInput && countrySelect) {
            postalInput.addEventListener("change", validateCountryWithPostalCode);
            countrySelect.addEventListener("change", validateCountryWithPostalCode);
        }

        validateCountryWithPostalCode(); // Lancer immédiatement la validation
    }

    // Attacher la validation dès que le DOM est prêt
    attachValidation();

    // ⚡ Écouter l'événement AJAX de PrestaShop
    prestashop.on('updatedAddressForm', function () {
        console.log("✅ Formulaire mis à jour, rebind de la validation...");
        attachValidation(); // Ré-attacher les événements après la mise à jour AJAX
    });
});

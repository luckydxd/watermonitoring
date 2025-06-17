document.addEventListener("DOMContentLoaded", function () {
    // Select All checkbox for each permission group
    document.querySelectorAll(".permission-group").forEach((group) => {
        const groupCheckbox = group.querySelector(".permission-group-checkbox");
        const permissionCheckboxes = group.querySelectorAll(
            ".permission-checkbox"
        );

        // When group checkbox is clicked
        groupCheckbox.addEventListener("change", function () {
            permissionCheckboxes.forEach((checkbox) => {
                checkbox.checked = this.checked;
            });
        });

        // When individual permission is clicked
        permissionCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", function () {
                const allChecked = [...permissionCheckboxes].every(
                    (cb) => cb.checked
                );
                groupCheckbox.checked = allChecked;
            });
        });
    });

    // Form validation
    const roleEditFormValidation = FormValidation.formValidation(
        document.getElementById("roleEditForm"),
        {
            fields: {
                "permissions[]": {
                    validators: {
                        choice: {
                            min: 1,
                            message: "Please select at least one permission",
                        },
                    },
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: "",
                    rowSelector: ".col-md-6",
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                autoFocus: new FormValidation.plugins.AutoFocus(),
            },
        }
    );
});

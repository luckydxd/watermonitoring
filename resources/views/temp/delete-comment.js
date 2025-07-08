function generateUniqueIdPreview() {
    const selectedOption = addDeviceTypeIdSelect.find("option:selected");

    if (selectedOption.length && selectedOption.val()) {
        const deviceTypeCode = selectedOption.data("code");

        if (deviceTypeCode) {
            const now = new Date();
            const year = String(now.getFullYear()).slice(-2);
            const month = String(now.getMonth() + 1).padStart(2, "0");
            const deviceVersion = "1";
            const randomPart = Math.floor(100 + Math.random() * 900);

            const uniqueId = `${year}${month}${deviceTypeCode}${deviceVersion}${randomPart}`;
            addDeviceIdInput.val(uniqueId);
        } else {
            addDeviceIdInput.val("");
        }
    } else {
        addDeviceIdInput.val("");
    }
}

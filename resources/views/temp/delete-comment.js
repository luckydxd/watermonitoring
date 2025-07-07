{{-- OFFCANVAS UNTUK FORM PENDAFTARAN ALAT --}}
<div
    class="offcanvas offcanvas-end"
    tabindex="-1"
    id="offcanvasRegisterDevice"
    aria-labelledby="offcanvasRegisterDeviceLabel"
>
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasRegisterDeviceLabel" class="offcanvas-title">
            Daftarkan Perangkat Baru
        </h5>
        <button
            type="button"
            class="btn-close text-reset"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>
    </div>
    <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-4">
        <form
            id="registerDeviceForm"
            class="pt-0"
            data-url="{{ route('user.device.register') }}"
            data-token="{{ csrf_token() }}"
        >
            <div class="mb-3">
                <label for="unique_id" class="form-label"
                    >ID Unik Perangkat</label
                >
                <input
                    type="text"
                    id="unique_id"
                    name="unique_id"
                    class="form-control"
                    placeholder="Masukkan ID yang tertera pada alat"
                    required
                />
            </div>

            <div
                class="mb-3"
                id="initial-reading-wrapper"
                style="display: none"
            >
                <label for="initial_meter_reading" class="form-label"
                    >Pembacaan Meteran Awal (Liter)</label
                >
                <input
                    type="number"
                    step="0.01"
                    id="initial_meter_reading"
                    name="initial_meter_reading"
                    class="form-control"
                    placeholder="Contoh: 1234.56"
                />
            </div>

            <button
                type="submit"
                class="btn btn-primary me-sm-3 me-1"
                id="submitRegisterBtn"
            >
                Daftarkan
            </button>
            <button
                type="reset"
                class="btn btn-label-secondary"
                data-bs-dismiss="offcanvas"
            >
                Batal
            </button>
        </form>
    </div>
</div>

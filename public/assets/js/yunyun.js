Swal = Swal.mixin({
    customClass: {
        popup: 'bg-dark text-white border border-secondary',
        confirmButton: 'btn btn-success mx-1',
        cancelButton: 'btn btn-danger mx-1',
        validationMessage: 'bg-secondary text-white'
    },
    buttonsStyling: false,
    showLoaderOnConfirm: true,
    allowOutsideClick: () => !Swal.isLoading(),
})


axios.defaults.validateStatus = function() {
    return true;
};
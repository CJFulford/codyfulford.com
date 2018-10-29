function editMuscle(muscleId, muscleName)
{
    $('form #muscle-id').val(muscleId);
    $('form #muscle-name').val(decodeURIComponent(muscleName)).attr('placeholder', decodeURIComponent(muscleName)).focus();
    $('#cancel-muscle-edit-button').removeClass('d-none');
}

function cancelMuscleEdit()
{
    $('form #muscle-id').val('');
    $('form #muscle-name').val('').attr('placeholder', '');
    $('#cancel-muscle-edit-button').addClass('d-none');
}
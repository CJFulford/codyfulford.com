var supersetCounter = 1;

$(document).ready(function ()
{
    // trigger the keyup event on page load to initialize each set with the edfault number of rounds
    $('.set-round-count').each(function (index, setRoundsInput)
    {
        changeNumberOfSets(index, setRoundsInput);
    });
});

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

function addMuscleToExercise()
{
    $('#exercise-muscles').append($('#exercise-muscles').children('div').first().clone());
}

function editExercise(exerciseId, exerciseName, exerciseDescription, exerciseRelatedMuscleIds)
{
    exerciseName = decodeURIComponent(exerciseName);
    exerciseDescription = decodeURIComponent(exerciseDescription);
    exerciseRelatedMuscleIds = JSON.parse(decodeURIComponent(exerciseRelatedMuscleIds));

    $('form #exercise-id').val(exerciseId);
    $('form #exercise-name').val(exerciseName).attr('placeholder', exerciseName).focus();
    $('form #exercise-description').val(exerciseDescription).attr('placeholder', exerciseDescription).focus();
    for (var i = 0; i < exerciseRelatedMuscleIds.length; i++)
    {
        $('#exercise-muscles').children('div').eq(i).find('select').val(exerciseRelatedMuscleIds[i]);
        addMuscleToExercise();
    }
    $('#cancel-exercise-edit-button').removeClass('d-none');
}

function cancelExerciseEdit()
{
    $('form #exercise-id').val('');
    $('form #exercise-name').val('').attr('placeholder', '');
    $('form #exercise-description').val('').attr('placeholder', '');
    addMuscleToExercise();
    $('#exercise-muscles').children('div:not(:last)').remove();
    $('#cancel-exercise-edit-button').addClass('d-none');
}

function gatherExerciseMuscles()
{
    var muscleIds = [];
    $('#exercise-muscles select').each(function (index, select)
    {
        if ($(select).val() !== '')
            muscleIds.push(parseInt($(select).val()));
    });
    $('form #related-muscles').val(JSON.stringify(muscleIds));
}

function addExerciseToSet(addButtonClicked)
{
    $(addButtonClicked).parentsUntil('.card').last().children('.exercise-select').last().after(
        $(addButtonClicked).parentsUntil('.card').last().children('.exercise-select').last().clone()
    );
}

function addSetToWorkout()
{
    $('#workout-sets').children('div').last().after(
        $('#workout-sets').children('div').last().clone()
    );
    $('#workout-sets').children('div').last().find('input[type=checkbox]').attr('id', 'superset-'+supersetCounter);
    $('#workout-sets').children('div').last().find('label').attr('for', 'superset-'+supersetCounter);
    supersetCounter++;
}

function removeSetFromWorkout(removeButtonClicked)
{
    // do not remove the last set
    if ($('#workout-sets').children('div').length > 1)
        $(removeButtonClicked).parentsUntil('.card-columns').last().remove();
    else
        alert('Cannot remove last set.');
}

function gatherWorkoutSets()
{
    var sets = [];
    $('#workout-sets .card').each(function (i, card)
    {
        var set = {
            'exercise-id-numbers': [],
            'superset': $(card).find('input[type=checkbox]').is(':checked')
        };
        $(card).find('select').each(function (j, select)
        {
            if ($(select).val() != '')
            set['exercise-id-numbers'].push($(select).val());
        });
        sets.push(set);
    });
    $('#workout-form #sets').val(JSON.stringify(sets));
    return true;
}

function changeNumberOfSets(setIndex, input)
{
    var previousRounds = $(input).data()['currentSetRounds'];
    var newRounds = $(input).val();
    // if newRounds is blank, then we assume that the user is going to enter a new numer of rounds and therefore we do nothing in anticipation of that.
    // if the user wants to do 0 rounds, they should enter 0
    if (newRounds != '')
    {
        while ($('.card:eq('+setIndex+') .card-body').children().first().find('.set-round:not(:first)').length != newRounds)
        {
            // adding a round
            if (previousRounds < newRounds)
            {
                $('.card:eq('+setIndex+') .card-body').children().each(function (rowIndex, exerciseRow)
                {
                    $(exerciseRow).find('.set-round').last().after($(exerciseRow).find('.set-round').last().clone(true));
                });
            }
            // removing a round
            else if (newRounds < previousRounds)
            {
                $('.card:eq('+setIndex+') .card-body').children().each(function (rowIndex, exerciseRow)
                {
                    $(exerciseRow).find('.set-round').last().remove();
                });
            }

            // reset the currentSetRounds data value to the now changed number
            $(input).data()['currentSetRounds'] = $('.card:eq('+setIndex+') .card-body').children().first().find('.set-round:not(:first)').length;
        }

        // show all set-rounds, but not the first
        $('.card:eq('+setIndex+') .card-body').children().each(function (rowIndex, exerciseRow)
        {
            $(exerciseRow).find('.set-round:not(:first)').removeClass('d-none');
        });

        // rename all inputs so that each is unique
        $('.card').each(function (setIndex, setCard)
        {
            $(setCard).find('.card-body').children().each(function (exerciseIndex, exerciseRow)
            {
                $(exerciseRow).find('.set-round:not(:first)').each(function (setRoundIndex, setRound)
                {
                    $(setRound).find('input').each(function (inputIndex, input)
                    {
                        $(input)[0].name = 'workout['+setIndex+']['+exerciseIndex+']['+setRoundIndex+']['+$(input).data()['name']+']';
                    });
                });
            });
        });
    }
}
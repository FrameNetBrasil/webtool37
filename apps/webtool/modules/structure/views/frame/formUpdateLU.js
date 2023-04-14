$(function () {
    $('#lbBFFHelp').tooltip({
        content: '<div>' +
    '<b>Different Perspective:</b> the LU imposes a perspective that is different from the one in the original frame.<br>' +
    '<b>Different Causative Alternation:</b> the LU requires a causative interpretation that is not present in the original frame, which may be either inchoative or stative.<br>' +
    '<b>Different Inchoative Alternation:</b> the LU requires an inchoative interpretation that is not present in the original frame, which may be either causative or stative.<br>' +
    '<b>Different Stative Alternation:</b> the LU requires a stative interpretation that is not present in the original frame, which may be either causative or inchoative.<br>' +
    '<b>Too Specific:</b> the LU is more generic than the background frame.<br>' +
    '<b>Too Generic:</b> the LU requires a frame more specific than the one available in the original database.<br>' +
    '<b>Different Entailment:</b> the LU has different entailments than the ones afforded by the original frame.<br>' +
    '<b>Different Coreness Status:</b> some non-core FE should be core in the target language.<br>' +
    '<b>Missing FE:</b> there should be a FE in the original frame that is missing. The missing FE must be listed in the Other/Missing FE field.<br>' +
    '<b>Other:</b> all other non-listed cases. The difference must be specified in the Other/Missing FE field.<br>' +
        '</div>'
    });
});

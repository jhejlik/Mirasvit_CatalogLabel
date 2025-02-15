function mLabelEnterCustomFunction(x, xparent) {
    txt= x.outerHTML;
    txtPrepared = txt.match(/mlabelcustomdata="(.*?)"/);
    if (typeof txtPrepared[1] != 'undefined') {
      currentmLabelEnterCustomUrl = xparent.href;
      xparent.href = txtPrepared[1];
    }
}

function mLabelOutCustomFunction(x, xparent) {
    if (currentmLabelEnterCustomUrl) {
        xparent.href = currentmLabelEnterCustomUrl;
    }
}
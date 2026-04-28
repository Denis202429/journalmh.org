using System;
using System.Linq;


namespace MorfParsingLibrary
{
    using A = MorfParser; 
    class FeaturesDeterminers
    {
        #region Settings
        #region NOUN
        //FACE
        string[] first_susch = { "ӑм", "ӗм", "ам", "ем", "м" };
        string[] second_susch = { "ÿ", "у", "ÿн", "ун", "ӑр", "ӗр" };
        string[] third_susch = { "и", "ин", "ӗ" };
        //PADEZHs
        //string[] osn = { "и", "у", "ÿ", "ӗ"};
        string[] rod = { "ӑн", "ӗн", "ин", "ÿн", "ун", "н", "сен" };
        string[] dat = { "на", "не", "а", "е" };
        string[] mest = { "ра", "ре", "та", "те", "че", "ти", "ри" };
        string[] isch = { "тан", "тен", "рен", "ран", "чен" };
        string[] tvor = { "па", "пе", "пала", "пеле", "палан", "пелен" };
        string[] lish = { "сӑр", "сӗр" };
        string[] prich_celevoy = { "шӑн", "шӗн" };
        //CHISLO
        string[] pluralnoun = { "сем", "сен" };
        #endregion
        #region VERB
        //FACE
        string[] first_verb = { "ӑп", "ӗп", "п", "ап", "еп", "ӑм", "ӗм" };
        string[] second_verb = { "ӑн", "ӗн", "ӑр", "ӗр", "ан", "ен" };
        string[] third_verb = { "ать", "ет", "аҫ", "еҫ", "ӗ", "ччӗр", "ччӑр", "ҫ", "нӑ", "нӗ", "ть", "т", "ччӗ", "тӑр", "тӗр" };
        
        //CHISLO
        string[] pl = { "ӑр", "ӗр", "аҫ", "еҫ", "ҫ", "ччӗр" };
        //VREMYA
        string[] nast_verb = { "ат", "ет", "ать", "аҫ", "еҫ", "ть", "п", "ап", "еп", "мас", "мес", "ан", "ен" };
        string[] prosh_verb = { "р", "ч", "ӑм", "ӗм", "атт", "етт", "сатт", "сетт", "сачч", "сечч", "атч", "етч", "ччӗ", "чӗ" };
        string[] bud_verb = { "ӗ" };

        string[] ambi_vremya_verb = { "ӑп", "ӗп", "ӑн", "ӗн", "ӑр", "ӗр" };
        string[] unknown_vremya = { "ма", "ме", "иччен" };
        //NEGATIVE
        string[] negative = { "мас", "мес", "маҫ", "меҫ", "м", "сӑр", "сӗр" };
        //INFINITIV
        string[] infinitiv = { "ма", "ме", "машкӑн", "мешкӗн" };

        #endregion
        #region PRONOUN

        #endregion
        #region CHISLITELNOE
        //FACE
        string[] first_chisl = { "сӑмӑр", "сӗмӗр" };
        string[] second_chisl = { "сӑр", "сӗр" };
        #endregion
        #region PRICHASTIE
        //
        string[] prich_mas = { "ас", "ес", "асшӑн", "есшӗн", "нӑ", "нӗ", "ан", "ен", "малла", "мелле" };
        //VREMYA
        string[] nast_prich = { "акан", "екен", "аканн", "екенн", "ан", "ен" };
        string[] bud_prich = { "ас", "ес", "асс", "есс", "асшӑн", "есшӗн" };
        string[] prosh_prich = { "нӑ", "нӗ", "м", "н" };

        string[] prosh_forall = { "ччӗ", "чӗ" };
        #endregion
#endregion
        #region FACE
        //noun
        internal string DetermineFaceOfSusch(string word, string[] aff_massiv)
        {
            bool sogl = ContextRules.Soglasnaya(word);//последняя буква согл или нет
            bool consistent = ContextRules.Consistency(word); //твердость/мягкость слова
            string padezh = DeterminePadezhOfSusch(word, aff_massiv);
            
            //Trace.WriteLine(consistency);
            if (sogl)
            {
                switch (padezh)
                {
                    case Constants.ROD_P:
                        switch (aff_massiv[0])
                        {
                            case "ӑн": return Constants.FACE1;
                            case "ÿн":
                            case "ун": return Constants.FACE2;
                            case "ӗн": if (consistent) return Constants.FACE3;
                                return "1,3е";
                        }
                        break;
                    case Constants.DAT_P:
                        switch (aff_massiv[0])
	                    {
                            case "а":
                            case "е":
                                return Constants.FACE1;
                            case "на":
                                return Constants.FACE2;
                            case "не": if (consistent) return Constants.FACE3;
                                return "2,3е";
	                    }
                        break;
                    case Constants.MEST_P:
                    case Constants.ISCH_P:
                        switch (aff_massiv[0])
                        {
                            case "та":
                            case "те":
                            case "тан":
                            case "тен":
                            case "ра":
                            case "ре":
                            case "ран":
                            case "рен":
                                return Constants.FACE1;
                            case "ÿн":
                            case "ун":
                                return Constants.FACE2;
                            case "ӗн":
                                return Constants.FACE3;
                        }
                        break;

                }
            }

            if (aff_massiv.Any(el => first_susch.Contains(el))) return Constants.FACE1;
            if (aff_massiv.Any(el => second_susch.Contains(el))) return Constants.FACE2;
            if (aff_massiv.Any(el => third_susch.Contains(el))) return Constants.FACE3;

            return Constants.FACE1;
        }
        //chislit-e
        internal string DetermineFaceOfChislitelnoe(string word, string[] aff_massiv)
        {
            if (aff_massiv.Any(el => first_chisl.Contains(el))) return Constants.FACE1;
            if (aff_massiv.Any(el => second_chisl.Contains(el))) return Constants.FACE2;
            if (aff_massiv.Any(el => "ӗ".Contains(el))) return Constants.FACE3;

            return Constants.FACE1;
        }
        //verb
        internal string DetermineFaceOfGlagol(string word, string[] aff_massiv)
        {
            if (aff_massiv.Contains("м") && (aff_massiv.Contains("ан") || aff_massiv.Contains("ен"))) return Constants.UNKNOWN; //нужно лучше
            if (aff_massiv.Any(el => first_verb.Contains(el))) return Constants.FACE1;
            if (aff_massiv.Any(el => second_verb.Contains(el))) return Constants.FACE2;
            if (aff_massiv.Any(el => third_verb.Contains(el))) return Constants.FACE3;
            return Constants.UNKNOWN;
        }
        //pronoun
        internal string DetermineFaceOfPronoun(string word, string[] aff_massiv)
        {
            for (int i = 0; i < Helper.pron_defaults.Length; i++)
            {
                string[] temp_mas = Helper.pron_defaults[i].Split(new char[] { ',' }, StringSplitOptions.RemoveEmptyEntries);
                if (word == temp_mas[0])
                {
                    return temp_mas[3];
                }
            }
            return Constants.FACE3;
        }
        //prichastie
        internal string DetermineFaceOfPrichastie(string word, string[] aff_massiv)
        {
            if (aff_massiv.Any(el => prich_mas.Contains(el))) return Constants.UNKNOWN;

            return DetermineFaceOfSusch(word, aff_massiv);
        }
        #endregion
        #region PADEZH

        //noun
        internal string DeterminePadezhOfSusch(string word, string[] aff_massiv)
        {
            if (aff_massiv.Contains("ӗн"))
            {
                if (aff_massiv.Contains("че")) return Constants.MEST_P;
                if (aff_massiv.Contains("чи")) return Constants.MEST_P;
                if (aff_massiv.Contains("чен")) return Constants.ROD_P;
                return Constants.ROD_P;
            }

            if (aff_massiv.Any(el => rod.Contains(el))) return Constants.ROD_P;
            if (aff_massiv.Any(el => dat.Contains(el))) return Constants.DAT_P;
            if (aff_massiv.Any(el => mest.Contains(el))) return Constants.MEST_P;
            if (aff_massiv.Any(el => isch.Contains(el))) return Constants.ISCH_P;
            if (aff_massiv.Any(el => tvor.Contains(el))) return Constants.TVOR_P;
            if (aff_massiv.Any(el => lish.Contains(el))) return Constants.LISH_P;
            if (aff_massiv.Any(el => prich_celevoy.Contains(el))) return Constants.PR_CEL_P;

            return Constants.OSN_P;
        }
        //prichastie
        internal string DeterminePadezhOfPrichastie(string word, string[] aff_massiv)
        {

            if (aff_massiv.Any(el => prich_mas.Contains(el))) return Constants.UNKNOWN;

            return DeterminePadezhOfSusch(word,  aff_massiv);
        }
        #endregion
        #region CHISLO
        //noun
        internal string DeterminePluralOfSusch(string word, string[] aff_massiv)
        {
            if (aff_massiv.Any(el => pluralnoun.Contains(el))) return Constants.MN_CHISLO;
            else return Constants.ED_CHISLO;
        }
        //мест
        internal string DeterminePluralOfPron(string word, string[] aff_massiv)
        {
            for (int i = 0; i < Helper.pron_defaults.Length; i++)
            {
                string[] temp_mas = Helper.pron_defaults[i].Split(new char[] { ',' }, StringSplitOptions.RemoveEmptyEntries);
                if (word == temp_mas[0])
                {
                    return temp_mas[1];
                }
            }
            return DeterminePluralOfSusch(word, aff_massiv);
        }
        //verb
        internal string DeterminePluralOfVerb(string word, string[] aff_massiv)
        {
            if (aff_massiv.Contains("м") && (aff_massiv.Contains("ан") || aff_massiv.Contains("ен"))) return Constants.UNKNOWN; //нужно лучше
            if (unknown_vremya.Contains(aff_massiv[aff_massiv.Length - 1])) return Constants.UNKNOWN;
            if (aff_massiv.Any(el => pl.Contains(el))) return Constants.MN_CHISLO;
            else return Constants.ED_CHISLO;
        }
        //prichastie
        internal string DeterminePluralOfPrichastie(string word, string[] aff_massiv)
        {
            if (aff_massiv.Any(el => prich_mas.Contains(el))) return Constants.UNKNOWN;

            return DeterminePluralOfSusch(word, aff_massiv);
        }
        #endregion
        #region VREMYA
        //verb
        internal string DetermineVremyaOfGlagol(string word, string[] aff_massiv)
        {
            string time = Constants.UNKNOWN;

            //if (aff_massiv.Any(el => unknown_vremya.Contains(el))) return "time";
            if (aff_massiv.Contains("м") && (aff_massiv.Contains("ан") || aff_massiv.Contains("ен"))) return Constants.PROSH_V; //нужно лучше

            if (aff_massiv.Any(el => prosh_verb.Contains(el))) return Constants.PROSH_V;
            if (aff_massiv.Any(el => nast_verb.Contains(el))) return Constants.NAST_V;
            if (aff_massiv.Any(el => bud_verb.Contains(el))) return Constants.BUD_V;

            if (aff_massiv.Any(el => ambi_vremya_verb.Contains(el))) 
            {
                if (aff_massiv.Contains("т")) time = Constants.NAST_V;
                else time = Constants.BUD_V;
            }
            return time;
        }
        //причастие
        internal string DetermineVremyaOfPichastie(string word, string[] aff_massiv)
        {
            string time = Constants.UNKNOWN;

            if (aff_massiv.Any(el => prosh_prich.Contains(el))) return Constants.PROSH_V;
            if (aff_massiv.Any(el => nast_prich.Contains(el))) return Constants.NAST_V;
            if (aff_massiv.Any(el => bud_prich.Contains(el))) return Constants.BUD_V;
            
            return time;
        }
        //остальные
        internal string DetermineVremyaOfOstalnoe(string word, string[] aff_massiv)
        {
            string time = Constants.UNKNOWN;
            if (aff_massiv.Any(el => prosh_forall.Contains(el))) return Constants.PROSH_V;
            return time;
        }
        #endregion
        #region NEGATIVE
        //verb
        internal string DetermineNegativeOfGlagol(string word, string[] aff_massiv)
        {
            string res = Constants.POSITIVE;
            if (aff_massiv.Any(el => negative.Contains(el))) return Constants.NEGATIVE;

            return res;
        }
        #endregion
        #region INFINITIV
        internal string DetermineInfinitivOfGlagol(string word, string[] aff_massiv)
        {
            if (infinitiv.Contains(aff_massiv[aff_massiv.Length - 1])) return Constants.INF;
            return Constants.NOTINF;
        }
        #endregion
        #region AFFINFO
        internal string DetermineAffixInfo(string word, string chastrechi, string[] aff_massiv)
        {
            int glagol_start, notglagol_start, prichastie_start;
            int glagol_end, notglagol_end, prichastie_end;

            glagol_start = notglagol_start = prichastie_start = 0;
            glagol_end = notglagol_end = prichastie_end = 0;

            string x = "";
            for (int i = 0; i < A.AffInfo.Length; i++)//плохо, исправить
            {
                if (A.AffInfo[i] == "[NOTGLAGOL]") notglagol_start = i;
                if (A.AffInfo[i] == "[GLAGOL]") glagol_start = i;
                if (A.AffInfo[i] == "[PRICHASTIE]") prichastie_start = i;

                if (A.AffInfo[i] == "[/NOTGLAGOL]") notglagol_end = i;
                if (A.AffInfo[i] == "[/GLAGOL]") glagol_end = i;
                if (A.AffInfo[i] == "[/PRICHASTIE]") prichastie_end = i;
            }
            int start, end;
            switch (chastrechi)
            {
                case Constants.VERB:
                case Constants.DEEPRICHASTIE:
                    start = glagol_start;
                    end = glagol_end;
                    break;
                case Constants.PRICHASTIE:
                    start = prichastie_start;
                    end = prichastie_end;
                    break;
                default:
                    start = notglagol_start;
                    end = notglagol_end;
                    break;
            }

            //new
            for (int j = aff_massiv.Length - 1; j >= 0; j--)
            {
                int temp_end = end - 1;
                for (int i = temp_end; i >= start; i--)
                {
                    string[] line = A.AffInfo[i].Split(new char[] { ';' }, StringSplitOptions.RemoveEmptyEntries);
                    string[] affs = line[0].Split(new char[] { '/' }, StringSplitOptions.RemoveEmptyEntries);

                    if (affs.Contains(aff_massiv[j]))
                    {
                        x = aff_massiv[j] + "-" + line[1] + "\r\n" + x;
                        temp_end = i;
                        break;
                    }
                }
            }
            
            return x;
        }
        #endregion
    }
}

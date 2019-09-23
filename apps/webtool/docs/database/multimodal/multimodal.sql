--
-- Comandos para simulação de anotação Multimodal
--

insert into paragraph (documentOrder, idDocument)
values (1, (select idDocument from document where entry = 'doc_multimodal_test1'));

insert into sentence (text, paragraphOrder, idParagraph, idLanguage)
values ('Quando a gente pensa na Escócia a primeira coisa que vem à mente é', 1, 49486, 1);

insert into sentence (text, paragraphOrder, idParagraph, idLanguage)
values ('homem de saia, whisky escocês e gaita de fole.', 2, 49486, 1);


insert into sentenceMM(idSentence, startTimestamp, endTimeStamp)
values (119701, '0:00:32.100', '0:00:35.320');

insert into sentenceMM(idSentence, startTimestamp, endTimeStamp)
values (119702, '0:00:35.320', '0:00:37.820');

insert into annotationsetmm (idSentenceMM) values (1);
insert into annotationsetmm (idSentenceMM) values (2);


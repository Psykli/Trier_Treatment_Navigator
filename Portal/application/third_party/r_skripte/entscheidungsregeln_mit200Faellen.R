#R Skript zur Erstellung des erwarteten Verlaufs sowie der Boundary f???r die nachfolgende Sitzung
#Version f???r Open Source Portal

#library("RODBC",lib.loc="C:/Program Files/R/R-3.2.5/library")
#library("car",lib.loc="C:/Program Files/R/R-3.2.5/library")
#library("fields",lib.loc="C:/Program Files/R/R-3.2.5/library")
#library("cluster",lib.loc="C:/Program Files/R/R-3.2.5/library")
#library("lme4",lib.loc="C:/Program Files/R/R-3.2.5/library")
#library("foreign",lib.loc="C:/Program Files/R/R-3.2.5/library")

library("RODBC")
library("car")
library(fields)
library(cluster)
library(lme4)
library(foreign)

args <- commandArgs(TRUE)

#Patient ausw???hlen f???r Test (sp???ter wird Fall ???ber PHP ???bergeben)
patient <- args[1]
aktuelle_session <- as.integer(args[2])

#Funktion, die die SPSS Funktion von mean.n erstellt (also wieviele Werte m???ssen vorhanden sein, damit Mittelwert gebildet werden kann)
mean.n   <- function(df, n) {
  ##-9999 wird zu NA recoded, restlicher Ablauf bleibt gleich, so wird verhindert, dass -9999 (=fehlerhafter Datenbankeintrag) in die Mittelwertberechnung flie???t
  df <- apply(as.matrix(df),c(1,2), function(x) {x[x==-9999] <- NA;x})
  means <- apply(as.matrix(df), 1, mean, na.rm = TRUE)
  nvalid <- apply(as.matrix(df), 1, function(df) sum(!is.na(df)))
  ifelse(nvalid >= n, means, NA)
}


#Verbindung alte Datenbank
myconn <-odbcDriverConnect("Driver=MySQL ODBC 8.0 Unicode Driver;Server=127.0.0.1; Database=portal; Uid=root;Pwd=; trusted_connection=yes")

#Verbindung Neue Datenbank
myconn1 <-odbcDriverConnect("Driver=MySQL ODBC 8.0 Unicode Driver;Server=127.0.0.1; Database=portal; Uid=root;Pwd=; trusted_connection=yes")


boundary <- sqlQuery(myconn1, sprintf("SELECT INSTANCE, HSCL_MEAN, BOUNDARY_NEXT, BOUNDARY_UEBERSCHRITTEN  from `entscheidungsregeln_hscl` WHERE CODE = '%s' AND INSTANCE = '%s'", patient, aktuelle_session-1, stringsAsFactors = T))

#hscl11_3 <- sqlQuery(myconn, sprintf("SELECT CODE,INSTANCE, HSC001, HSC002, HSC003, HSC004, HSC005, HSC006, HSC007, HSC008,
#                                     HSC009, HSC010, HSC011 FROM `2 hscl-11` WHERE CODE = '%s' ", patient))

hscl11_3 <- sqlQuery(myconn, sprintf("SELECT CODE,INSTANCE, HSC001, HSC002, HSC003, HSC004, HSC005, HSC006, HSC007, HSC008,
                                     HSC009, HSC010, HSC011 FROM `hscl-11` WHERE CODE = '%s' AND INSTANCE REGEXP '^[0-9]+' ", patient))

#Mittelwert HSCL_11
hscl11_3$hscl_mean <- mean.n(hscl11_3[3:13], 9)

#Nach Sitzung sortieren
hscl11_3 <- hscl11_3[order(hscl11_3$INSTANCE, decreasing=F),]

#Instance numerisch machen
hscl11_3$INSTANCE <- as.numeric(hscl11_3$INSTANCE)


#HSCL-11 Datens???tze erstellen mit der jeweiligen Sitzung
hscl11_2 <- hscl11_3[hscl11_3$INSTANCE==(aktuelle_session),]

#Umbennen der Sitzung
#names(hscl11_2)[names(hscl11_2)=="hscl_mean"] <- "hscl_mean_next"
hscl11_3 <- subset(hscl11_3, INSTANCE==2)



#Datensatz einlesen mit HSCL-Verlaufswerten
#v1 entspricht HSCL zur Sitzung 2
daten_fbimp_wide <- read.table("./test.dat", sep = ";")

daten_fbimp_long <- reshape(data = daten_fbimp_wide, varying = 3:31, v.names = "hscl", timevar = "INSTANCE", times = 1:29, idvar = "CODE", direction = "long")

#Instance numerisch machen
daten_fbimp_long$INSTANCE <- as.numeric(daten_fbimp_long$INSTANCE)

#CODE k???rzen
daten_fbimp_long$CODE<-substr(daten_fbimp_long$CODE, 1,7)
daten_fbimp_wide$CODE<-substr(daten_fbimp_wide$CODE, 1,7)



daten_fbimp_long$INSTANCE <- daten_fbimp_long$INSTANCE -1



#Festlegen der HSCL-Cutoffs (wie weit d???rfen die n???chsten Nachbarn zu Pr??? entfernt sein)
cufoff.init.score <- 0.5


#Nur F???lle ausw???hlen, die einen ???hnlichen HSCL-Werte haben
daten_fbimp_wide$include <- ifelse(abs(hscl11_3$hscl_mean - daten_fbimp_wide[,3])<=cufoff.init.score, 1, 0)

#Subdatensatz mit F???llen mit ???hnlichem HSCL-Wert
dattesa_next <- subset(daten_fbimp_wide, include==1)

#Fall wieder raussschmei???en, falls im Datensatz
dattesa_next <- subset(dattesa_next, dattesa_next$CODE != patient)

#Changesscore f???r den Patienten ermitteln
hscl11_3$change_hscl <- hscl11_2$hscl_mean - hscl11_3$hscl_mean



#Hier dann die Pr???diktoren rein!
Praediktoren<-c("hscl_mean", "change_hscl")#, "change_hscl") 


#Hier dann die Pr???diktoren rein!
Praediktoren2_next<-c("hscl_mean") 

#Die Pr???diktoren von dem angeklickten Patienten ausw???hlen
gesamt_klein <- subset(hscl11_3, select=Praediktoren)

#Die Pr???diktoren von dem angeklickten Patienten ausw???hlen
gesamt_klein2_next <- subset(hscl11_3, select=Praediktoren2_next)

#Datensatz mit F???llen erstellen, die ???hnliche Ausgangsbelastung haben und den HSCL-Wert der aktuellen Sitzung einf???gen
gesamt <- dattesa_next[,c(3,aktuelle_session +1)]

#Changescore zw. erster und aktueller Sitzung berechnen
gesamt$change_hscl <- gesamt[,2] - gesamt[,1]

#Umbennen des Ausgangswerts
names(gesamt)[1] <- "hscl_mean"

#Nur Ausgangswert und Changescore im Datensatz behalten
gesamt <- gesamt[,c(1,3)]



# 
data.NN_2_next <- rbind(gesamt_klein, gesamt)

data.NN <- as.matrix(data.NN_2_next[,1])

#WENN PATIENT KEINEN MATCH HAT GIBT ES BISLANG KEINEN AUSWEG.


ed.matrix.1 <- daisy(data.NN, metric="gower", stand=F)

ed.matrix.3 <- daisy(data.NN_2_next, metric="gower", stand=F)

#leere Matrix erstellen
b.1 <- matrix(0,nrow=nrow(data.NN),ncol=nrow(data.NN))

b.3 <- matrix(0,nrow=nrow(data.NN_2_next),ncol=nrow(data.NN_2_next))

#Vector aus Daiys als Matrix darstellen
b.1<-as.matrix(ed.matrix.1)

b.3<-as.matrix(ed.matrix.3)

#Nur die Spalte des Indexpatienten i ausw???hlen
c.final.1 <- data.frame(b.1[,1])

c.final.3 <- data.frame(b.3[,1])

c.final.1 <- data.frame(c.final.1[-c(1),])

c.final.3 <- data.frame(c.final.3[-c(1),])

#R???nge bilden f???r die Abst???nde
c.final.1$rank<-rank(c.final.1[,1], ties.method="first")

c.final.3$rank<-rank(c.final.3[,1], ties.method="first")

#Code anderweitig vorher wieder in den Datensatz holen!
#Die Gruppenvariable hinzuf???gen
c.final.1$CODE <- dattesa_next$CODE

c.final.3$CODE <- dattesa_next$CODE


#Sortieren nach dem Rang
c.final.1 <- c.final.1[order(c.final.1$rank, decreasing=F),]

c.final.3 <- c.final.3[order(c.final.3$rank, decreasing=F),]

#Erstellen eines Subsets, welches nur die n???chsten 30 Patienten enth???lt 
NN_ID.1 <- subset(c.final.1, (rank <=30))

NN_ID.3 <- subset(c.final.3, (rank <=30))


data_hlm.1 <- daten_fbimp_long[daten_fbimp_long$CODE %in% NN_ID.1$CODE,]

data_hlm.3 <- daten_fbimp_long[daten_fbimp_long$CODE %in% NN_ID.3$CODE,]


data_hlm.1$sitzung_log <- log(data_hlm.1$INSTANCE + 1)

#data_hlm.3$sitzung_log <- log(data_hlm.3$INSTANCE + 1)



#Linear model mit logarithmiter Zeit (f???r Modell von der ersten bis zur 30. Sitzung)
lmer.1<- lmer(hscl ~ 1 + sitzung_log + (1 + sitzung_log | CODE), data = data_hlm.1, REML = FALSE)

#Linear model mit linearer Zeit (f???r Modell von der ersten bis zur 30. Sitzung)
lmer.3<- lmer(hscl ~ 1 + INSTANCE + (1 + INSTANCE | CODE), data = data_hlm.3, REML = FALSE)


exp_val.1 <- vector()

try(VarComponents.1 <- as.data.frame(VarCorr(lmer.1)))

#for(i in 1:(nrow(hscl11_3)+1))
for(i in 1:30)
{
  
  
  session<-log(i)
  #Erwartete Werte f???r jeden Fall berechnen
  try(exp_val.1[[i]]<-((coef(summary(lmer.1))[1])+(coef(summary(lmer.1))[2]*session)))
  
  
  
}



exp_val.3 <- vector()
neg_val90.3 <- vector()

try(VarComponents.3 <- as.data.frame(VarCorr(lmer.3)))

for(i in 1:1)
{
  
  neg_val90.3[[i]]<-NA
  
  #Varianzkomponenten aus jedem lmer ziehen
  #try(VarComponents.1[[i]] <- as.data.frame(VarCorr(lmer.1[[i]])))
  
  
  session<-i
  #Erwartete Werte f???r jeden Fall berechnen
  try(exp_val.3[[i]]<-((coef(summary(lmer.3))[1])+(coef(summary(lmer.3))[2]*session)))
  
  #Boundaries berechnen
  try(neg_val90.3[[i]]<-exp_val.3[[i]] + 1.645*(sqrt((VarComponents.3[1,4] + 2*VarComponents.3[3,4] + VarComponents.3[2,4]) *(session^2)+(VarComponents.3[4,4]))))
  
  
}



boundary_ueberschritten <- -1

#Schaut ob in der Boundary Tabelle bereits etwas enthalten ist, wenn ja:
#wird geschaut, ob aktuell der Cut-Off ???berschritten wird oder nicht und dementsprechend wir die Variable
#boundary_ueberschritten mit -1 bzw. der alten Sitzung (bei vorherigem ???berschreiten) oder er aktuellen Sitzung beschrieben
if(nrow(boundary) > 0 ){
  ifelse(boundary$BOUNDARY_NEXT < hscl11_2$hscl_mean,
         ifelse(boundary$BOUNDARY_UEBERSCHRITTEN > 0, boundary_ueberschritten <- boundary$BOUNDARY_UEBERSCHRITTEN, boundary_ueberschritten <- aktuelle_session)
         ,boundary_ueberschritten <- -1)
} 

#Reliable Change HSCL festlegen
RCI <- 0.38


if(boundary_ueberschritten>0 )
{
  if(boundary_ueberschritten != aktuelle_session){
    boundary2 <- sqlQuery(myconn1, sprintf("SELECT INSTANCE, HSCL_MEAN, BOUNDARY_NEXT, BOUNDARY_UEBERSCHRITTEN  from `entscheidungsregeln_hscl` WHERE CODE = '%s' AND INSTANCE = '%s'", patient, boundary_ueberschritten, stringsAsFactors = T))
    hscl_ref <- boundary2$HSCL_MEAN
  } else {
    hscl_ref <- hscl11_2$hscl_mean
  }
  
  ifelse(neg_val90.3> (hscl_ref - RCI), neg_val90.3 <- (hscl_ref - RCI), neg_val90.3)
}

id <- NULL

id <- sqlQuery(myconn1, sprintf("SELECT ID from `entscheidungsregeln_hscl` WHERE CODE = '%s' AND INSTANCE = '%s'", patient,aktuelle_session))

#test <- "test"

ifelse(nrow(id)==1, 
       
       #Aktualisiert Feedbacksignale f???r den Patienten in der Datenbank
       ifelse(is.na(hscl11_2$hscl_mean),
              sqlQuery(myconn1, sprintf("UPDATE `entscheidungsregeln_hscl` SET HSCL_MEAN = NULL, BOUNDARY_NEXT = '%s', BOUNDARY_UEBERSCHRITTEN = '%s' WHERE ID = '%s'",neg_val90.3, boundary_ueberschritten, id)),
              sqlQuery(myconn1, sprintf("UPDATE `entscheidungsregeln_hscl` SET HSCL_MEAN = '%s', BOUNDARY_NEXT = '%s', BOUNDARY_UEBERSCHRITTEN = '%s' WHERE ID = '%s'", hscl11_2$hscl_mean, neg_val90.3, boundary_ueberschritten, id))
       )
       ,
       #F???gt Feedbacksignale f???r den Patienten in die Datenbank, wenn Patient noch nicht vorhanden
       ifelse(is.na(hscl11_2$hscl_mean),
              sqlQuery(myconn1, sprintf("INSERT INTO `entscheidungsregeln_hscl` (CODE,INSTANCE,BOUNDARY_NEXT,BOUNDARY_UEBERSCHRITTEN) VALUES ('%s','%s', '%s', '%s')", patient, aktuelle_session, neg_val90.3, boundary_ueberschritten)),
              sqlQuery(myconn1, sprintf("INSERT INTO `entscheidungsregeln_hscl` (CODE,INSTANCE,HSCL_MEAN,BOUNDARY_NEXT,BOUNDARY_UEBERSCHRITTEN) VALUES ('%s', '%s', '%s', '%s', '%s')", patient, aktuelle_session, hscl11_2$hscl_mean, neg_val90.3, boundary_ueberschritten))
       )
)



id2 <- sqlQuery(myconn1, sprintf("SELECT ID from `entscheidungsregeln_hscl2` WHERE CODE = '%s'", patient))



ifelse(nrow(id2)==1, 
       
       #Aktualisiert Feedbacksignale f???r den Patienten in der Datenbank
       #sqlQuery(myconn, sprintf("UPDATE `entscheidungsregeln_hscl2` SET HSCL_MEAN = '%s', BOUNDARY_NEXT = '%s', BOUNDARY_UEBERSCHRITTEN = '%s' WHERE ID = '%s'", gesamt$hscl_mean_next, e[2], boundary_ueberschritten, id))
       print("Hier k???nnte IHR Update stehen!")
       ,
       #F???gt Feedbacksignale f???r den Patienten in die Datenbank, wenn Patient noch nicht vorhanden
       sqlQuery(myconn1, sprintf("INSERT INTO `entscheidungsregeln_hscl2` (CODE,EXPECTED_VALUE1,EXPECTED_VALUE2,EXPECTED_VALUE3,EXPECTED_VALUE4,EXPECTED_VALUE5,EXPECTED_VALUE6,EXPECTED_VALUE7,EXPECTED_VALUE8
                                 ,EXPECTED_VALUE9,EXPECTED_VALUE10,EXPECTED_VALUE11,EXPECTED_VALUE12,EXPECTED_VALUE13,EXPECTED_VALUE14,EXPECTED_VALUE15,EXPECTED_VALUE16,EXPECTED_VALUE17,EXPECTED_VALUE18
                                 ,EXPECTED_VALUE19,EXPECTED_VALUE20,EXPECTED_VALUE21, EXPECTED_VALUE22,EXPECTED_VALUE23,EXPECTED_VALUE24,EXPECTED_VALUE25,EXPECTED_VALUE26,EXPECTED_VALUE27,EXPECTED_VALUE28
                                 ,EXPECTED_VALUE29,EXPECTED_VALUE30) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
                                 '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", patient, exp_val.1[1], exp_val.1[2], exp_val.1[3], exp_val.1[4], exp_val.1[5], exp_val.1[6], exp_val.1[7], exp_val.1[8], exp_val.1[9], exp_val.1[10],
                                 exp_val.1[11], exp_val.1[12], exp_val.1[13], exp_val.1[14], exp_val.1[15], exp_val.1[16], exp_val.1[17], exp_val.1[18], exp_val.1[19], exp_val.1[20], exp_val.1[21], exp_val.1[22], exp_val.1[23], exp_val.1[24], exp_val.1[25], exp_val.1[26], exp_val.1[27], exp_val.1[28], exp_val.1[29], exp_val.1[30]))
       
)








///////////////////////////////////
//Copyright 2012 Adam Zolyak and Matthew Gorbsky
//Das Bot v2.0
///////////////////////////////////

///////////
//Int

//Int: Overall
    int overallStep = 0;
    //overallStep = 0 = Reset and Ready to Start
    //overallStep = 1 = RFID Read / Getting Data
    //overallStep = 2 = RFID Got Data / Printing
    //overallStep = 3 = Printed / Pouring and Measuring
    //overallStep = 4 = Poured / Saving Data
    //overallStep = 5 = Saved Data / Printing
    
    //to store Ethernet connection response
    String valueDataCall1Response;
    int DataCall1Success = 0;
    int DataCall1Tries = 0;
    
    //Ensure RFID is read 2x to ensure its the same value
    int valueNumRFIDReads = 0;
      
    //to store read RFID value
    String valueRFID;
    
    //Set Pin for output for solenoid
    int SolenoidPin = 7;
    
    //Track time flow meter is idle
    int valueNumFlowIdle = 0;
    
    //Track total drunk
    int valueTotalDrunk1 = 0;
    int valueTotalDrunk2 = 0;
    int valueTotalDrunk3 = 0;
    
    int stringOneExists = -1;
    
    //Add Software Serial support
    #include "mySoftwareSerial.h"

//Int: RGB LED
    int redPin = A3;
    int bluePin = A5;
    int greenPin = A4;

//Int: RFID
    int RFIDResetPin = 6;
    
    //Set piezzo buzzer pin
    int PiezzoBuzzerPin = 5;
    
    //mySoftwareSerial rfidSerial(10, 11); // RX, TX
    
//Int: Ethernet
    #include <SPI.h>
    
    #include <EthernetClient.h>
    #include <Ethernet.h>
    #include <EthernetServer.h>
    
    #include <max6675.h>
    
    // the media access control (ethernet hardware) address for the shield:
    byte mac[] = { 0x90, 0xA2, 0xDA, 0x00, 0x2F, 0x03 };  
    //the IP address for the shield:
    byte ip[] = { 192, 168, 1, 125 };    
    byte server[] = { 67, 205, 51, 120 }; // tinkurlab.com 67.205.51.120
    
    EthernetClient client;

//Int: Flowmeter
    
    #define NO_PORTB_PINCHANGES // to indicate that port b will not be used for pin change interrupts
    #define NO_PORTD_PINCHANGES // to indicate that port b will not be used for pin change interrupts

    #include <PinChangeInt.h>
    
    #define FlowPIN1 A0
    #define FlowPIN2 A1
    #define FlowPIN3 A2
    
    int drunk1 = 0;
    int drunk2 = 0;
    int drunk3 = 0;
    
    void addDrink1()
    {
    drunk1++;
    }
  
   void addDrink2()
    {
    drunk2++;
    }
    
   void addDrink3()
    {
    drunk3++;
    }

//Int: Thermal Printer

  #include <Thermal.h>
  
  int printer_RX_Pin = 8;
  int printer_TX_Pin = 9;
  
  Thermal printer(printer_RX_Pin, printer_TX_Pin, 19200);
      

///////////
//Setup

void setup(){
  
  //Setup: Ethernet
  Ethernet.begin(mac, ip);
  
  Serial.begin(9600);
  
  pinMode(SolenoidPin, OUTPUT);  

  //Setup: RFID
  pinMode(RFIDResetPin, OUTPUT);
  digitalWrite(RFIDResetPin, HIGH);
  
  pinMode(PiezzoBuzzerPin, OUTPUT);
  
  Serial.println("RFID Online!");
 
  // wait for MAX chip to stabilize
  delay(5000);
  
  Serial.println("Ethernet Online!");
  
//Setup: Flowmeter

  pinMode(FlowPIN1, INPUT); 
  digitalWrite(FlowPIN1, HIGH);
  PCintPort::attachInterrupt(FlowPIN1, addDrink1, CHANGE);  // add more attachInterrupt code as required
  pinMode(FlowPIN2, INPUT); 
  digitalWrite(FlowPIN2, HIGH);
  PCintPort::attachInterrupt(FlowPIN2, addDrink2, CHANGE);
  pinMode(FlowPIN3, INPUT); 
  digitalWrite(FlowPIN3, HIGH);
  PCintPort::attachInterrupt(FlowPIN3, addDrink3, CHANGE);

  Serial.println("Flowmeter Online!");
  
  //Setup: Thermal Printer
  printer.setHeatTime(160); // 80 is default from page 23 of datasheet. Controls speed of printing and darkness
  printer.setHeatInterval(2); // 2 is default from page 23 of datasheet. Controls speed of printing and darkness
  printer.setPrintDensity(15); // Not sure what the defaut is. Testing shows the max helps darken text. From page 23.
  printer.setPrintBreakTime(15); // Not sure what the defaut is. Testing shows the max helps darken text. From page 23.
  
  Serial.println("Thermal Printer Online!");
  
}

///////////
//Loop

void loop(){

//Step 0

  Serial.println("Step 0");
  
  //Turn on Ready Light
  analogWrite(redPin, 0);
  analogWrite(greenPin, 255);
  analogWrite(bluePin, 0);
  
  while(overallStep == 0){ 
    
      char tagString[13];
      int index = 0;
      boolean reading = false;
        
          while(Serial.available()){
        
            int readByte = Serial.read(); //read next available byte
        
            if(readByte == 2) reading = true; //begining of tag
            if(readByte == 3) reading = false; //end of tag
        
            if(reading && readByte != 2 && readByte != 10 && readByte != 13){
              //store the tag
              tagString[index] = readByte;
              index ++;
              
            }
          }
        
         //valueRFID = tagString;
         
           if(strlen(tagString) == 0){ //empty, no need to contunue
           
           }
           else{
             valueNumRFIDReads = valueNumRFIDReads + 1; 
             
             //Serial.println(tagString); //read out any unknown tag
             //Serial.println(valueNumRFIDReads);
            
                if(valueNumRFIDReads > 3){
                    overallStep = 1;
                    valueRFID = tagString;
                    valueRFID = valueRFID.substring(0,12);
                    Serial.println(valueRFID);
    
                    for (int i=1; i <= 3; i++){
                      beep(50);
                      analogWrite(redPin, 0);
                      analogWrite(greenPin, 255);
                      analogWrite(bluePin, 0);
                      
                      delay(100);
                      
                      analogWrite(redPin, 0);
                      analogWrite(greenPin, 0);
                      analogWrite(bluePin, 0);
                  
                      delay(100);
                   } 
                    
                }
              
           }
         
        clearTag(tagString); //Clear the char of all value
        resetReader(); //reset the RFID reader
        
   }
  
  //Step 1
  
  Serial.println("Step 1");
  
  //digitalWrite(ReadyLightPin, LOW);
  
  while(overallStep == 1){
    
          while(DataCall1Tries < 3){
      
          Serial.println("connecting...");
      
          if (client.connect(server, 80)) {
            
          DataCall1Success = 1;
       
          Serial.println("connected");
            
          client.print("GET /projects/dasbot/arduino.php?rfid=");
          client.print(valueRFID);
          client.println(" HTTP/1.0");
          client.println();
              
          } else {
            Serial.println("connection failed");
            
            DataCall1Tries = DataCall1Tries + 1;
          }
          
          }
    
  // if there are incoming bytes available 
  // from the server, read them and print them:
  if (client.available()) {
    char c = client.read();
  //Serial.print(c);
    
    valueDataCall1Response += c;
  }

  // if the server's disconnected, stop the client:
  if (!client.connected()) {
    Serial.println();
    Serial.println("disconnecting.");
    client.stop();
    
    //valueDataCall1Response = valueDataCall1Response.substring(241, valueDataCall1Response.length());

    Serial.println(valueDataCall1Response);
    
    String stringToFind = "No beer for you";
    stringOneExists = valueDataCall1Response.indexOf(stringToFind);
    Serial.println("Exists: ");
    Serial.println(stringOneExists);

    if(DataCall1Success == 1){
       
        overallStep = 2;    
    }else{
        overallStep = 6;
    }
    
    
    
  }  
    }
    
//Step 2
    
    Serial.println("Step 2");
    
    while(overallStep == 2){
      
      Serial.print("Print Out: ");
      Serial.println(valueDataCall1Response);
      
      int bodyTag = valueDataCall1Response.indexOf("text/html");
      
      //Thermal Printer      
      printer.justify('L'); //sets text justification (right, left, center) accepts 'L', 'C', 'R'
   
      printer.println(valueDataCall1Response.substring(bodyTag + 9)); //print line
      printer.feed();
      printer.feed();
      printer.feed();
      
        
      if(stringOneExists == -1){
      
        //continue to next step
        overallStep = 3;
        
        Serial.println("Break A");
      }else{
      
        //end b/c user hasn't reigstered
        overallStep = 6;
        Serial.println("Break B");
      }
 
  }
    
//Step 3
  
    Serial.println("Step 3");
    
    while(overallStep == 3){
      
      //Turn on Ready Light
      analogWrite(redPin, 0);
      analogWrite(greenPin, 0);
      analogWrite(bluePin, 255); 
      
      //open solenoid valve    
      digitalWrite(SolenoidPin, HIGH);   
      
      //reset to 0 incase interupt function was called prematurely
      drunk1 = 0; 
      drunk2 = 0; 
      drunk3 = 0;  
  
      //allow for 8 seconds of idle activity before turning off solenoid
      while(valueNumFlowIdle <= 8){
        
          delay(1000);
          
          valueTotalDrunk1 = valueTotalDrunk1 + drunk1;
          valueTotalDrunk2 = valueTotalDrunk2 + drunk2;
          valueTotalDrunk3 = valueTotalDrunk3 + drunk3;
          
          Serial.print("Tap 1: ");
          Serial.println(drunk1);
          
          Serial.print("Total Tap 1: ");
          Serial.println(valueTotalDrunk1);
          
          Serial.print("Tap 2: ");
          Serial.println(drunk2);
          
          Serial.print("Total Tap 2: ");
          Serial.println(valueTotalDrunk2);
          
          Serial.print("Tap 3: ");
          Serial.println(drunk3);
          
          Serial.print("Total Tap 3: ");
          Serial.println(valueTotalDrunk3);
      
          if(drunk1 == 0){
            if(drunk2 == 0){
              if(drunk3 == 0){
                valueNumFlowIdle = valueNumFlowIdle + 1;
              }
            }
          }
          
          Serial.print("Drunk Idle Seconds: ");
          Serial.println(valueNumFlowIdle);
          
          drunk1 = 0;
          drunk2 = 0;
          drunk3 = 0;
    
      }
   
      if(valueTotalDrunk1 > 0){
            Serial.println("Had some beer");
            overallStep = 4;
      }else if(valueTotalDrunk2 > 0){
            Serial.println("Had some beer");
            overallStep = 4;
      }else if(valueTotalDrunk3 > 0){
            Serial.println("Had some beer");
            overallStep = 4; 
      }else{
            Serial.println("Had no beer");
            overallStep = 6;
      }
   
      //close solenoid valve  
      digitalWrite(SolenoidPin, LOW);   
      
      //Turn off ready light
      analogWrite(redPin, 0);
      analogWrite(greenPin, 0);
      analogWrite(bluePin, 0); 

    }
    
//Step 4
  
    Serial.println("Step 4");
    
    valueDataCall1Response = "";
    DataCall1Success = 0;
    DataCall1Tries = 0;
    
    while(overallStep == 4){
      
    while(DataCall1Tries < 3){
      
          Serial.println("connecting...");
      
          if (client.connect(server, 80)) {
            
          DataCall1Success = 1;
       
          Serial.println("connected");
            
          client.print("GET /projects/dasbot/arduino.php?rfid=");
          client.print(valueRFID);
          client.print("&consumed1=");
          client.print(valueTotalDrunk1);
          client.print("&consumed2=");
          client.print(valueTotalDrunk2);
          client.print("&consumed3=");
          client.print(valueTotalDrunk3);
          client.println(" HTTP/1.0");
          client.println();
              
          } else {
            Serial.print("connection failed, try #");
            Serial.println(DataCall1Tries);
            
            DataCall1Tries = DataCall1Tries + 1;
          }
          
          }
    
          // if there are incoming bytes available 
          // from the server, read them and print them:
          if (client.available()) {
            char c = client.read();
            //Serial.print(c);
            
            valueDataCall1Response += c;
          }
        
          // if the server's disconnected, stop the client:
          if (!client.connected()) {
            Serial.println();
            Serial.println("disconnecting.");
            client.stop();
            
            //valueDataCall1Response = valueDataCall1Response.substring(241, valueDataCall1Response.length());
        
            Serial.println(valueDataCall1Response);
        
            if(DataCall1Success == 1){
               
                overallStep = 5;    
            }else{
                overallStep = 6;
            }
          }
  }
  
  
//Step 5
  
    Serial.println("Step 5");
    
    while(overallStep == 5){ 
     
      //Thermal Printer      
      int bodyTag = valueDataCall1Response.indexOf("text/html");
      
      //Thermal Printer      
      printer.justify('L'); //sets text justification (right, left, center) accepts 'L', 'C', 'R'
   
      printer.println(valueDataCall1Response.substring(bodyTag + 9)); //print line
      printer.feed();
      printer.feed();
      printer.feed();
   
      overallStep = 6; 
      
    }
    
//Step 6
  
    Serial.println("Step 6");
    
    while(overallStep == 6){ 
     
      //cleanup variables
    
        //reset RFID reads
        valueNumRFIDReads = 0;
        
        //reset RFID value
        
        //valueRFID = 0 ; Not sure how to null string
        
        //reset drunk values
        drunk1 = 0;
        valueTotalDrunk1 = 0;
        drunk2 = 0;
        valueTotalDrunk2 = 0;
        drunk3 = 0;
        valueTotalDrunk3 = 0;
        valueNumFlowIdle = 0;
        
        //reset print text
        valueDataCall1Response = "";
        
        DataCall1Success = 0;
        
        DataCall1Tries = 0;
       
        overallStep = 0; 
    }
    
}

///////////
//Functions: Beep
void beep(unsigned char delayms){
  analogWrite(PiezzoBuzzerPin, 50);      // Almost any value can be used except 0 and 255
                           // experiment to get the best tone
  delay(delayms);          // wait for a delayms ms
  analogWrite(PiezzoBuzzerPin, 0);       // 0 turns it off
  delay(delayms);          // wait for a delayms ms   
} 


//Functiond:

//Functions: RFID

    
    void resetReader(){
    ///////////////////////////////////
    //Reset the RFID reader to read again.
    ///////////////////////////////////
      digitalWrite(RFIDResetPin, LOW);
      digitalWrite(RFIDResetPin, HIGH);
      delay(150);
    }
    
    void clearTag(char one[]){
    ///////////////////////////////////
    //clear the char array by filling with null - ASCII 0
    //Will think same tag has been read otherwise
    ///////////////////////////////////
      for(int i = 0; i < strlen(one); i++){
        one[i] = 0;
      }
    }
    
    boolean compareTag(char one[], char two[]){
    ///////////////////////////////////
    //compare two value to see if same,
    //strcmp not working 100% so we do this
    ///////////////////////////////////
    
      if(strlen(one) == 0) return false; //empty
    
      for(int i = 0; i < 12; i++){
        if(one[i] != two[i]) return false;
      }
    
      return true; //no mismatches
    }

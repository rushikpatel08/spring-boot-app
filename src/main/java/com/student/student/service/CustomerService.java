package com.student.student.service;
import java.util.ArrayList;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.student.student.entity.Customer;
import com.student.student.repository.CustomerRepository;

@Service
public class CustomerService {

    @Autowired
    private CustomerRepository customerRepository;

    //Insert records into table
    public Customer InsertCustomer(Customer customer)
    {
        return customerRepository.save(customer);
    }
    //Display records From table
    public List<Customer> DisplayCustomer()
    {
        List<Customer> customer= new ArrayList<>();
        customerRepository.findAll().forEach(customer::add);
        return customer;
    }
    //Display records From table for particular id
    public Customer DisplayCustomer(Integer id)
    {
       return customerRepository.findById(id).orElseThrow();
    }
    //Delete records From table
    public void DeleteCustomer(Integer id)
    {
        customerRepository.deleteById(id);
    }
    //Update records From table
    public Customer UpdateCustomer(Customer customer)
    {
        customerRepository.findById(customer.getId()).orElseThrow();
        return customerRepository.save(customer);  
    }
}
